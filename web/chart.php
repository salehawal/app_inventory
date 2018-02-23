<?php
require_once('inc/core.php');
require_once('inc/app.php');
sys_init(); // init page session
$conn = db_connect();
user_login_check();
?>
<!doctype html>
<html>
<head>
  <title>inventory info collection</title>
  <script src="js/funcs.js"></script>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
  <link rel="stylesheet" type="text/css" href="css/bootstrap-3.3.5-dist/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/reset.css">
  <link rel="stylesheet" type="text/css" href="css/bootstrap-fileinput-master/css/fileinput.min.css">
  <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="css/ionicons.min.css">
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <link rel="stylesheet" type="text/css" href="css/main_mobile.css">
  <link rel="stylesheet" type="text/css" href="css/admin-te.min.css">
  <link href="css/_all-skins.min.css" rel="stylesheet" type="text/css" />
  <link href="css/blue.css" rel="stylesheet" type="text/css" />
  <link href="css/datepicker3.css" rel="stylesheet" type="text/css" />

  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="css/bootstrap-fileinput-master/js/fileinput.min.js"></script>
  <script src="js/respond.min.js"></script>
  <script src="js/html5shiv.min.js"></script>
</head>
<body>
  <div class="content-wrapper">
      <?php $showmenu = false; include('inc/header.php'); ?>
      <!-- main content -->
      <section class="content">
        <div class="row">
          <div class="col-xs-12">
            <!-- interactive chart -->
            <div class="box box-primary">
              <div class="box-header with-border">
                <i class="fa fa-bar-chart-o"></i>
                <h3 class="box-title">interactive area chart</h3>
                <div class="box-tools pull-right">
                  real time
                  <div class="btn-group" id="realtime" data-toggle="btn-toggle">
                    <button type="button" class="btn btn-default btn-xs active" data-toggle="on">on</button>
                    <button type="button" class="btn btn-default btn-xs" data-toggle="off">off</button>
                  </div>
                </div>
              </div>
              <div class="box-body">
                <div id="interactive" style="height: 300px;"></div>
              </div><!-- /.box-body-->
            </div><!-- /.box -->

          </div><!-- /.col -->
        </div><!-- /.row -->

        <div class="row">
          <div class="col-md-6">
            <!-- line chart -->
            <div class="box box-primary">
              <div class="box-header with-border">
                <i class="fa fa-bar-chart-o"></i>
                <h3 class="box-title">line chart</h3>
                <div class="box-tools pull-right">
                  <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div id="line-chart" style="height: 300px;"></div>
              </div><!-- /.box-body-->
            </div><!-- /.box -->

            <!-- area chart -->
            <div class="box box-primary">
              <div class="box-header with-border">
                <i class="fa fa-bar-chart-o"></i>
                <h3 class="box-title">full width area chart</h3>
                <div class="box-tools pull-right">
                  <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div id="area-chart" style="height: 338px;" class="full-width-chart"></div>
              </div><!-- /.box-body-->
            </div><!-- /.box -->

          </div><!-- /.col -->

          <div class="col-md-6">
            <!-- bar chart -->
            <div class="box box-primary">
              <div class="box-header with-border">
                <i class="fa fa-bar-chart-o"></i>
                <h3 class="box-title">bar chart</h3>
                <div class="box-tools pull-right">
                  <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div id="bar-chart" style="height: 300px;"></div>
              </div><!-- /.box-body-->
            </div><!-- /.box -->

            <!-- donut chart -->
            <div class="box box-primary">
              <div class="box-header with-border">
                <i class="fa fa-bar-chart-o"></i>
                <h3 class="box-title">donut chart</h3>
                <div class="box-tools pull-right">
                  <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div id="donut-chart" style="height: 300px;"></div>
              </div><!-- /.box-body-->
            </div><!-- /.box -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </section><!-- /.content -->
  </div>
<script src="http://127.0.0.1/workspace/ws/cwebapp/theme/admin/plugins/jquery/jQuery-2.1.4.min.js"></script>
<!-- bootstrap 3.3.2 js -->
<script src="http://127.0.0.1/workspace/ws/cwebapp/theme/admin/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- fastclick -->
<script src='http://127.0.0.1/workspace/ws/cwebapp/theme/admin/plugins/fastclick/fastclick.min.js'></script>
<!-- adminlte app -->
<script src="http://127.0.0.1/workspace/ws/cwebapp/theme/admin/dist/js/app.min.js" type="text/javascript"></script>
<!-- adminlte for demo purposes -->
<script src="http://127.0.0.1/workspace/ws/cwebapp/theme/admin/dist/js/demo.js" type="text/javascript"></script>
<!-- flot charts -->
<script src="http://127.0.0.1/workspace/ws/cwebapp/theme/admin/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
<!-- flot resize plugin - allows the chart to redraw when the window is resized -->
<script src="http://127.0.0.1/workspace/ws/cwebapp/theme/admin/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
<!-- flot pie plugin - also used to draw donut charts -->
<script src="http://127.0.0.1/workspace/ws/cwebapp/theme/admin/plugins/flot/jquery.flot.pie.min.js" type="text/javascript"></script>
<!-- flot categories plugin - used to draw bar charts -->
<script src="http://127.0.0.1/workspace/ws/cwebapp/theme/admin/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
<!-- page script -->
<script type="text/javascript">

      $(function () {

        /*
         * flot interactive chart
         * -----------------------
         */
        // we use an inline data source in the example, usually data would
        // be fetched from a server
        var data = [], totalpoints = 100;
        function getrandomdata() {

          if (data.length > 0)
            data = data.slice(1);

          // do a random walk
          while (data.length < totalpoints) {

            var prev = data.length > 0 ? data[data.length - 1] : 50,
                    y = prev + math.random() * 10 - 5;

            if (y < 0) {
              y = 0;
            } else if (y > 100) {
              y = 100;
            }

            data.push(y);
          }

          // zip the generated y values with the x values
          var res = [];
          for (var i = 0; i < data.length; ++i) {
            res.push([i, data[i]]);
          }

          return res;
        }

        var interactive_plot = $.plot("#interactive", [getrandomdata()], {
          grid: {
            bordercolor: "#f3f3f3",
            borderwidth: 1,
            tickcolor: "#f3f3f3"
          },
          series: {
            shadowsize: 0, // drawing is faster without shadows
            color: "#3c8dbc"
          },
          lines: {
            fill: true, //converts the line chart to area chart
            color: "#3c8dbc"
          },
          yaxis: {
            min: 0,
            max: 100,
            show: true
          },
          xaxis: {
            show: true
          }
        });

        var updateinterval = 500; //fetch data ever x milliseconds
        var realtime = "on"; //if == to on then fetch data every x seconds. else stop fetching
        function update() {

          interactive_plot.setdata([getrandomdata()]);

          // since the axes don't change, we don't need to call plot.setupgrid()
          interactive_plot.draw();
          if (realtime === "on")
            settimeout(update, updateinterval);
        }

        //initialize realtime data fetching
        if (realtime === "on") {
          update();
        }
        //realtime toggle
        $("#realtime .btn").click(function () {
          if ($(this).data("toggle") === "on") {
            realtime = "on";
          }
          else {
            realtime = "off";
          }
          update();
        });
        /*
         * end interactive chart
         */


        /*
         * line chart
         * ----------
         */
        //line randomly generated data

        var sin = [], cos = [];
        for (var i = 0; i < 14; i += 0.5) {
          sin.push([i, math.sin(i)]);
          cos.push([i, math.cos(i)]);
        }
        var line_data1 = {
          data: sin,
          color: "#3c8dbc"
        };
        var line_data2 = {
          data: cos,
          color: "#00c0ef"
        };
        $.plot("#line-chart", [line_data1, line_data2], {
          grid: {
            hoverable: true,
            bordercolor: "#f3f3f3",
            borderwidth: 1,
            tickcolor: "#f3f3f3"
          },
          series: {
            shadowsize: 0,
            lines: {
              show: true
            },
            points: {
              show: true
            }
          },
          lines: {
            fill: false,
            color: ["#3c8dbc", "#f56954"]
          },
          yaxis: {
            show: true,
          },
          xaxis: {
            show: true
          }
        });
        //initialize tooltip on hover
        $("<div class='tooltip-inner' id='line-chart-tooltip'></div>").css({
          position: "absolute",
          display: "none",
          opacity: 0.8
        }).appendto("body");
        $("#line-chart").bind("plothover", function (event, pos, item) {

          if (item) {
            var x = item.datapoint[0].tofixed(2),
                    y = item.datapoint[1].tofixed(2);

            $("#line-chart-tooltip").html(item.series.label + " of " + x + " = " + y)
                    .css({top: item.pagey + 5, left: item.pagex + 5})
                    .fadein(200);
          } else {
            $("#line-chart-tooltip").hide();
          }

        });
        /* end line chart */

        /*
         * full width static area chart
         * -----------------
         */
        var areadata = [[2, 88.0], [3, 93.3], [4, 102.0], [5, 108.5], [6, 115.7], [7, 115.6],
          [8, 124.6], [9, 130.3], [10, 134.3], [11, 141.4], [12, 146.5], [13, 151.7], [14, 159.9],
          [15, 165.4], [16, 167.8], [17, 168.7], [18, 169.5], [19, 168.0]];
        $.plot("#area-chart", [areadata], {
          grid: {
            borderwidth: 0
          },
          series: {
            shadowsize: 0, // drawing is faster without shadows
            color: "#00c0ef"
          },
          lines: {
            fill: true //converts the line chart to area chart
          },
          yaxis: {
            show: false
          },
          xaxis: {
            show: false
          }
        });

        /* end area chart */

        /*
         * bar chart
         * ---------
         */

        var bar_data = {
          data: [["january", 10], ["february", 8], ["march", 4], ["april", 13], ["may", 17], ["june", 9]],
          color: "#3c8dbc"
        };
        $.plot("#bar-chart", [bar_data], {
          grid: {
            borderwidth: 1,
            bordercolor: "#f3f3f3",
            tickcolor: "#f3f3f3"
          },
          series: {
            bars: {
              show: true,
              barwidth: 0.5,
              align: "center"
            }
          },
          xaxis: {
            mode: "categories",
            ticklength: 0
          }
        });
        /* end bar chart */

        /*
         * donut chart
         * -----------
         */

        var donutdata = [
          {label: "series2", data: 30, color: "#3c8dbc"},
          {label: "series3", data: 20, color: "#0073b7"},
          {label: "series4", data: 50, color: "#00c0ef"}
        ];
        $.plot("#donut-chart", donutdata, {
          series: {
            pie: {
              show: true,
              radius: 1,
              innerradius: 0.5,
              label: {
                show: true,
                radius: 2 / 3,
                formatter: labelformatter,
                threshold: 0.1
              }

            }
          },
          legend: {
            show: false
          }
        });
        /*
         * end donut chart
         */

      });

      /*
       * custom label formatter
       * ----------------------
       */
      function labelformatter(label, series) {
        return "<div style='font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;'>"
                + label
                + "<br/>"
                + math.round(series.percent) + "%</div>";
      }
    </script>
</body>
</html>