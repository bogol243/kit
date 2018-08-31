{{include "common.pheader"}}
<!-- /.row -->
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-user fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $numCustomers; ?></div>
                        <div>Customers</div>
                    </div>
                </div>
            </div>
            <a href="../anketa.php">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-tasks fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">12</div>
                        <div>New Tasks!</div>
                    </div>
                </div>
            </div>
            <a href="#">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">

    </div>
    <div class="col-lg-3 col-md-6">

    </div>
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-8">


        <!-- /.panel -->
    </div>
    <!-- /.col-lg-8 -->
    <div class="col-lg-4">

        <!-- /.panel .chat-panel -->
    </div>
    <!-- /.col-lg-4 -->
</div>
<!-- /.row -->

<div id="chart1" style="margin-top:20px; margin-left:20px; width:500px; height:300px;"></div>
<script class="code" type="text/javascript">$(document).ready(function(){
        $.jqplot.config.enablePlugins = true;
        var s1 = [2, 6, 7, 10];
        var ticks = ['a', 'b', 'c', 'd'];

        plot1 = $.jqplot('chart1', [s1], {
            // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
            animate: !$.jqplot.use_excanvas,
            seriesDefaults:{
                renderer:$.jqplot.BarRenderer,
                pointLabels: { show: true }
            },
            axes: {
                xaxis: {
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks: ticks
                }
            },
            highlighter: { show: false }
        });
    });
</script>
<script class="include" type="text/javascript" src="/js/jPlot/jquery.jqplot.min.js"></script>
<script class="include" type="text/javascript" src="/js/jPlot/jqplot.barRenderer.min.js"></script>
<script class="include" type="text/javascript" src="/js/jPlot/jqplot.pieRenderer.min.js"></script>
<script class="include" type="text/javascript" src="/js/jPlot/jqplot.categoryAxisRenderer.min.js"></script>
<script class="include" type="text/javascript" src="/js/jPlot/jqplot.pointLabels.min.js"></script>

{{include "common.pfooter"}}