$(function() {
    var Charts = {
        _HYPHY_REGEX: /-([a-z])/g,
        _cleanAttr: function(t) {
            delete t.chart, delete t.value, delete t.labels
        },
        doughnut: function(element) {
            var attrData = $.extend({}, $(element).data()),
                data = eval(attrData.value);
            Charts._cleanAttr(attrData);
            var options = $.extend({
                responsive: !0,
                animation: !1,
                segmentStrokeColor: "#fff",
                segmentStrokeWidth: 2,
                percentageInnerCutout: 80
            }, attrData);
            new Chart(element.getContext("2d")).Doughnut(data, options)
        },
        bar: function(element) {
            var attrData = $.extend({}, $(element).data()),
                data = {
                    labels: eval(attrData.labels),
                    datasets: eval(attrData.value).map(function(t, e) {
                        return $.extend({
                            fillColor: e % 2 ? "#42a5f5" : "#1bc98e",
                            strokeColor: "transparent"
                        }, t)
                    })
                };
            Charts._cleanAttr(attrData);
            var options = $.extend({
                responsive: !0,
                animation: !1,
                scaleShowVerticalLines: !1,
                scaleOverride: !0,
                scaleSteps: 4,
                scaleStepWidth: 25,
                scaleStartValue: 0,
                barValueSpacing: 10,
                scaleFontColor: "rgba(0,0,0,.4)",
                scaleFontSize: 14,
                scaleLineColor: "rgba(0,0,0,.05)",
                scaleGridLineColor: "rgba(0,0,0,.05)",
                barDatasetSpacing: 2
            }, attrData);
            new Chart(element.getContext("2d")).Bar(data, options)
        },
        line: function(element) {
            var attrData = $.extend({}, $(element).data()),
                data = {
                    labels: eval(attrData.labels),
                    datasets: eval(attrData.value).map(function(t) {
                        return $.extend({
                            fillColor: "rgba(66, 165, 245, .2)",
                            strokeColor: "#42a5f5",
                            pointStrokeColor: "#fff"
                        }, t)
                    })
                };
            Charts._cleanAttr(attrData);
            var options = $.extend({
                animation: !1,
                responsive: !0,
                bezierCurve: !0,
                bezierCurveTension: .25,
                scaleShowVerticalLines: !1,
                pointDot: !1,
                tooltipTemplate: "<%= value %>",
                scaleOverride: !0,
                scaleSteps: 3,
                scaleStepWidth: 1e3,
                scaleStartValue: 2e3,
                scaleLineColor: "rgba(0,0,0,.05)",
                scaleGridLineColor: "rgba(0,0,0,.05)",
                scaleFontColor: "rgba(0,0,0,.4)",
                scaleFontSize: 14,
                scaleLabel: function(t) {
                    return t.value.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
                }
            }, attrData);
            new Chart(element.getContext("2d")).Line(data, options)
        },
        "spark-line": function(element) {
            var attrData = $.extend({}, $(element).data()),
                data = {
                    labels: eval(attrData.labels),
                    datasets: eval(attrData.value).map(function(t) {
                        return $.extend({
                            fillColor: "rgba(255,255,255,.3)",
                            strokeColor: "#fff",
                            pointStrokeColor: "#fff"
                        }, t)
                    })
                };
            Charts._cleanAttr(attrData);
            var options = $.extend({
                animation: !1,
                responsive: !0,
                bezierCurve: !0,
                bezierCurveTension: .25,
                showScale: !1,
                pointDotRadius: 0,
                pointDotStrokeWidth: 0,
                pointDot: !1,
                showTooltips: !1
            }, attrData);
            new Chart(element.getContext("2d")).Line(data, options)
        }
    };

    $(document).on("redraw.bs.charts", function() {
        $("[data-chart]").each(function() {
            $(this).is(":visible") && Charts[$(this).attr("data-chart")](this)
        })
    }).trigger("redraw.bs.charts");

    $(document).on("shown.bs.tab", function() {
        $(document).trigger("redraw.bs.charts")
    });
});