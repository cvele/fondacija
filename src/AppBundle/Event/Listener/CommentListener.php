<?php

namespace AppBundle\Event\Listener;

use Doctrine\ORM\EntityManager;
use FOS\CommentBundle\Model\CommentManagerInterface;
use FOS\CommentBundle\Event\CommentEvent;
use Hip\MandrillBundle\Message;

class CommentListener
{
    /**
     * @var CommentManagerInterface
     */
    private $commentManager;

    /**
     *
     * @var Doctrine
     */
    private $em;

    private $mandrill;

    private $templating;

    /**
     * Constructor.
     *
     * @param CommentManagerInterface $commentManager
     */
    public function __construct(CommentManagerInterface $commentManager,  EntityManager $em, $mandrill, $templating)
    {
        $this->commentManager = $commentManager;
        $this->em             = $em;
        $this->mandrill       = $mandrill;
        $this->templating     = $templating;
    }

    /**
     *
     * @param \FOS\CommentBundle\Event\CommentEvent $event
     */
    public function onCommentPrePersist(CommentEvent $event)
    {
        $comment = $event->getComment();

        $body = $comment->getBody();

        preg_match_all("/@([A-Za-z0-9\_\-]+)/i", $body, $matches);

        $user_repo     = $this->em->getRepository('AppBundle:User');
        foreach ($matches[1] as $key => $value)
        {
            $mentioned_user = $user_repo->findOneBy(['username'=>$value]);
            if ($mentioned_user == null)
            {
                continue;
            }

            $body = str_replace($matches[0][$key], "@[".$mentioned_user->getUsername()."](user:".$mentioned_user->getId().")", $body);

            $comment->setBody($body);
        }
    }

    /**
     *
     * @param \FOS\CommentBundle\Event\CommentEvent $event
     */
    public function onCommentPersist(CommentEvent $event)
    {
        $comment = $event->getComment();

        if ($this->commentManager->isNewComment($comment) == false)
        {
            return;
        }

        $body      = $comment->getBody();
        $author    = $comment->getAuthor();
        $permalink = $comment->getThread()->getPermalink();

        $res = preg_match("/app\/contact\/([0-9]+)\/show/i", $permalink, $contact_match);
        if ($res == false)
        {
            $this->onCommentPersistDocument($event);
            return;
        }

        $contact_id   = $contact_match[1];
        $contact_repo = $this->em->getRepository('AppBundle:Person');
        $contact      = $contact_repo->find($contact_id);

        preg_match_all("/@\[([A-Za-z0-9\_\-]+)\]\(user\:([0-9]+)\)/i", $body, $matches);

        $user_repo     = $this->em->getRepository('AppBundle:User');
        foreach ($matches[2] as $key => $user_id)
        {
            $mentioned_user = $user_repo->find($user_id);
            if ($mentioned_user == null)
            {
                continue;
            }

            $html = $this->templating->render('AppBundle:Email:note_mention.html.twig', [
                    'mentionee' => $mentioned_user,
                    'mentioner' => $author,
                    'permalink' => $permalink,
                    'contact'   => $contact,
                    'comment'   => $comment
                ]);

            $message = new Message();
            $message
                ->addTo($mentioned_user->getEmail())
                ->setSubject('New note for '.$contact->getLastname().", ".$contact->getFirstname())
                ->setHtml($html);

            $result = $this->mandrill->send($message);

        }
    }

    public function onCommentPersistDocument(CommentEvent $event)
    {
        $comment = $event->getComment();

        if ($this->commentManager->isNewComment($comment) == false)
        {
            return;
        }

        $body      = $comment->getBody();
        $author    = $comment->getAuthor();
        $permalink = $comment->getThread()->getPermalink();

        $res = preg_match("/app\/document\/([0-9]+)\/show/i", $permalink, $document_match);
        if ($res == false)
        {
            return;
        }

        $document_id   = $document_match[1];
        $document_repo = $this->em->getRepository('AppBundle:Document');
        $document      = $document_repo->find($document_id);

        preg_match_all("/@\[([A-Za-z0-9\_\-]+)\]\(user\:([0-9]+)\)/i", $body, $matches);

        $user_repo     = $this->em->getRepository('AppBundle:User');
        foreach ($matches[2] as $key => $user_id)
        {
            $mentioned_user = $user_repo->find($user_id);
            if ($mentioned_user == null)
            {
                continue;
            }

            $html = $this->templating->render('AppBundle:Email:document_mention.html.twig', [
                    'mentionee' => $mentioned_user,
                    'mentioner' => $author,
                    'permalink' => $permalink,
                    'document'  => $document,
                    'comment'   => $comment
                ]);

            $message = new Message();
            $message
                ->addTo($mentioned_user->getEmail())
                ->setSubject('New note for document ' . $document->getTitle())
                ->setHtml($html);

            $result = $this->mandrill->send($message);

        }
    }
}