<?php
$GLOBALS["SUPPORT"] = [];

require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "GET, POST");


require_once 'config/setup.php';
require_once 'config/ui.php';

$setup = new Setup();
$ui = new UI();


/*
self::write( 'valid', date( DATE_ATOM, strtotime( $lifetime , time() ) ) );
$valid = ( ( new DateTime( $_SESSION['valid'] ) ) > ( new DateTime() ) );
( ( $_SESSION['last_access'] ) < ( date( DATE_ATOM, strtotime( "+190 seconds" , time() ) ) ) )
<?php echo json_encode(DeviceTAC::isValid(), JSON_PRETTY_PRINT) . "\n\n" . json_encode( ( ( $_SESSION['last_access'] ) < ( date( DATE_ATOM, strtotime( "+190 seconds" , time() ) ) ) ), JSON_PRETTY_PRINT) . "\n\n" . json_encode($_SESSION, JSON_PRETTY_PRINT); ?>
*/
if ( isset( $_COOKIE["REGISTRATIONDIALOG"] ) || ( !DeviceTAC::isValid() && ( !isset( $_SESSION['last_registration'] ) || ( isset( $_SESSION['last_registration'] ) && ( $_SESSION['last_registration'] <  date( DATE_ATOM, strtotime( "+" . SESSION_MAX_LOCKOUTTIME . " seconds" , time() ) ) ) ) ) ) ) { 
    $expiration = "+180 seconds";
    DeviceTAC::abort();
    DeviceTAC::restore( $expiration );
    DeviceTAC::write( 'last_registration', new DateTime('now') );
    DeviceTAC::write( 'expiration', $expiration );
    DeviceTAC::commit();
}


if (defined("ERROR")) {

} else {
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta name="viewport" content="initial-scale=0.5, maximum-scale=1, user-scalable=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="manifest" href="<?php echo MANIFEST;?>">
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo ICON_APPLE;?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo ICON_32;?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo ICON_16;?>">
        <!--<link rel='stylesheet' href='https://map.geo.admin.ch/master/25078c3/1809261047/1809261047/style/app.css'>-->
        <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Libre+Baskerville:400,400italic'>
        <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
        <link rel='stylesheet' href='https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css'>
        <link rel='stylesheet' href='https://cdn.datatables.net/searchpanes/1.0.1/css/searchPanes.dataTables.min.css'>
        <link rel='stylesheet' href='https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css'>
        <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css'>

        <?php echo '        <link rel="stylesheet" href="'. COMPONENT_PATH . '/' . 'application.css' . '?version=' . time() . '">'; ?>
    </head>

    <body>
        <script src="https://api3.geo.admin.ch/loader.js?lang=de&version=4.4.2"></script>
        <script src='https://use.fontawesome.com/releases/v5.4.1/js/all.js'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.2/jquery.xdomainrequest.min.js'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.4/dist/typeahead.bundle.js'></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
        <script src="https://cdn.datatables.net/searchpanes/1.0.1/js/dataTables.searchPanes.min.js"></script>
        <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
        <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>

        <script>
        <?php
            if ( DeviceTAC::isValid() ) {
                $registered = 0;
                if ( json_decode( DeviceTAC::read( 'person' ) )->display !== "unbekannt" ) {
                    $registered = 1;
                }
                define("REGISTERED_DEVICE", $registered);
                echo "\nvar REGISTERED_DEVICE = " . $registered . ";\n";
            } else {
                define("REGISTERED_DEVICE", -2);
                echo "\nvar REGISTERED_DEVICE = -2;\n";
            }
            
            echo "\nvar QUERY_STRING = " . json_encode($_GET, JSON_PRETTY_PRINT) . ";\n\n";

            //echo "\nvar SESSION = " . json_encode($_SESSION, JSON_PRETTY_PRINT) . ";\n";
            //echo "\nvar SUPPORT = " . json_encode($GLOBALS["SUPPORT"], JSON_PRETTY_PRINT) . ";\n\n";
        ?>
        </script>

        <?php 
        $ui->init();
        echo '        <script src="' . COMPONENT_PATH . '/' . 'page.js' . '?version=' . time() . '"></script>'; 
        echo '        <script src="' . PLUGIN_PATH . '/' . '000_app.js' . '?version=' . time() . '"></script>'; 
        ?>

        <!-- Warper -->
        <div id="wrapper">
            <div style="margin: 5px;" class="panel panel-default">
                <div class="panel-heading">
                    <div class="" role="group" aria-label="Header Group">
                      <h3 class="panel-title">Geräteregistration</h3>

                      <div class="btn-toolbar modal-confirm" role="toolbar" aria-label="Toolbar with button groups">
                        <div class="btn-group btn-group-xs mr-2" role="group" aria-label="Default Buttongroup">
                          <button class="btn btn-default" data-dismiss="modal">Schliessen</button>
                        </div>
                        <div class="btn-group btn-group-xs mr-2" role="group" aria-label="Extended Buttongroup">
                          <button id="REGISTRATION_PROHIBITED_BUTTON" class="btn btn-danger" data-dismiss="modal" onclick="document.cookie = zsld.REGISTRATION_PROHIBITED;">Nein danke!</button>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="panel-body" style="height: calc(100vh - 58px);">

                <?php
                if ( DeviceTAC::isValid() ) {
                ?>
                    <div id="tracking-container" lass="form-horizontal collapse in" aria-expanded="true" style="">
                        <div id="zsld-tracking-settings" class="form-group"></div>
                    </div>

                    <script>
                      // register device if not done
                      var deviceRegistration = function() {
                        if (REGISTERED_DEVICE > -1) {
                          console.info('Device identification successful ');
                          passed('Device identification successful');

                          if (REGISTERED_DEVICE < 2) {
                            var identification = '';
                            identification += '    <div class="row">';
                            identification += '        <div class="col-xs-4 col-md-4">Geräte Identifikation</div>';
                            identification += '        <div id="zsld-tracking-registration" class="col-xs-8 col-md-8">erfolgreich</div>';
                            identification += '    </div>';
                            $("#zsld-tracking-success").append(identification);
                            var information = '';
                            information += '    <div class="row">';
                            information += '        <div class="col-xs-4 col-md-4">Geräte ID</div>';
                            information += '        <div id="zsld-tracking-device" class="col-xs-8 col-md-8">******************</div>';
                            information += '    </div>';
                            $("#zsld-tracking-information").append(information);
                          }

                          $http_tracking = new Rest(
                            function(response) {
                              var device;
                              if (REGISTERED_DEVICE > 0) {
                                device = response.objects[0];
                              } else {
                                //device = JSON.parse(response);
                                device = response;
                              }
                              console.info('Device registration successful ', device);
                              passed('Device registration successful');

                              var registration = '';
                              registration += '    <div class="row">';
                              registration += '        <div class="col-xs-4 col-md-4">Geräte Registration</div>';
                              registration += '        <div id="zsld-tracking-registration" class="col-xs-8 col-md-8">' + (device && device.data ? device.data : "") + '</div>';
                              registration += '    </div>';
                              $('#zsld-tracking-success').html(registration);

                              if (device && device.oid) {
                                $('#zsld-tracking-device').text(device.oid.substring(0, 39) + '…');
                              }

                              if (REGISTERED_DEVICE < 1) {
                                $('#person').hide();
                                $('#REGISTRATION_PROHIBITED_BUTTON').hide();
                              }

                              //deviceTracking();

                              REGISTERED_DEVICE = 2;
                            },
                            function(data) {
                              console.error('Error attempting to register your device ', data);
                              failed('Error attempting to register your device');

                              var registration = '';
                              registration += '    <div class="row">';
                              registration += '        <div class="col-xs-4 col-md-4">Geräte Registration</div>';
                              registration += '        <div id="zsld-tracking-registration" class="col-xs-8 col-md-8">Fehler bei der Registration deines Gerätes</div>';
                              registration += '    </div>';
                              $('#zsld-tracking-error').html(registration);


                              REGISTERED_DEVICE = -1;
                            }
                          );

                          if (REGISTERED_DEVICE < 1) {
                            var personal = '';
                            personal += '    <input id="person" type="text" class="form-control typeahead" data-provide="typeahead" style="border-radius: 4px; width: calc(100vw - 42px);"';
                            personal += '        title="Vorname Name  …"';
                            personal += '        placeholder="Suche Vorname Name  …">';
                            $("#zsld-tracking-settings").append(personal);

                            // Initialize bloodhound
                            zsld.ADZS = new Bloodhound({
                              limit: (screen.width < 1200 ? 5 : 12),
                              datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
                              queryTokenizer: Bloodhound.tokenizers.whitespace,
                              remote: {
                                url: URL_ZSLD_PERSON,
                                filter: function(persons) {
                                  return persons.results;
                                },
                                ajax: {
                                  xhrFields: {
                                    withCredentials: true
                                  }
                                }
                              }
                            });
                            // This kicks off the loading and processing of local and prefetch data,
                            // the suggestion engine will be useless until it is initialized
                            zsld.ADZS.initialize();

                            // Initialize typeahead input
                            $('#person').typeahead(null, {
                              name: 'persons',
                              displayKey: function(person) {
                                return person.name;
                              },
                              source: zsld.ADZS.ttAdapter(),
                              templates: {
                                suggestion: function(person) {
                                  return '<p>' + person.name + '</p>';
                                }
                              }
                            });

                            // When a result is selected
                            $('#person').on('typeahead:selected', function(evt, person, suggName) {
                              console.debug("[$('#person').on('typeahead:selected'])] result ", person);

                              person.properties.trackme = $("#zsld-tracking-check-input").is(':checked');
                              person.properties.notifymc = $("#zsld-tracking-check-kp-input").is(':checked');

                              $http_tracking.post(URL_REGISTRATION, JSON.stringify({
                                "id": person.properties["IPN"],
                                "display": "<b>" + person.properties["Vorname"] + " " + person.properties["Name"] + "</b>",
                                "properties": person.properties,
                                "forceconcurrentobjects": false,
                                "concurrentobjectsallowed": false
                              }));
                            });
                          }

                          if (REGISTERED_DEVICE == 1) {
                            //var url = URL_TRANSACTIONS.replace(/(type=)[^&]*/, '$1objectstore');
                            //url = url.replace(/(object=)[^&]*/, '$1devices');
                            //url += '&id=0';
                            //$http_tracking.get(url);
                          }
                        } else {
                          console.error('Error attempting to identify your device ', REGISTRATION_ERROR);
                          failed('Error attempting to identify your device');

                          var identification = '';
                          identification += '    <div class="row">';
                          identification += '        <div class="col-xs-4 col-md-4">Geräte Identifikation</div>';
                          identification += '        <div id="zsld-tracking-identification" class="col-xs-8 col-md-8">Fehler bei der Identifikation deines Gerätes</div>';
                          identification += '    </div>';
                          $('#zsld-tracking-error').html(identification);
                        }
                      };

                      var deviceTracking = function() {
                        if (navigator.geolocation) {
                          console.info('Locating Device … ', 'TRACK_ME:', (TRACK_ME ? 'TRUE' : 'FALSE'), '/','NOTIFY_MC:', (NOTIFY_MC ? 'TRUE' : 'FALSE'));
                          passed('Locating Device …');

                          //var status = '<div id="zsld-tracking-locating" class="alert alert-success fade in" role="alert">Locating … </div>';
                          //$("#zsld-tracking-status").append(status);
                          var locating = '';
                          locating += '    <div class="row">';
                          locating += '        <div class="col-xs-4 col-md-4">Geolocation Service</div>';
                          locating += '        <div id="zsld-tracking-locating" class="col-xs-8 col-md-8">Gerät lokalisieren … </div>';
                          locating += '    </div>';
                          $('#zsld-tracking-success').html(locating);


                          /**
                          * 
                          * 
                          * 
                          * 
                          * 
                          */
                          navigator.geolocation.getCurrentPosition(
                            function(position) {
                              console.info('Device tracking successful ', position);
                              passed('Device tracking successful');

                              var location = '';
                              location += '    <div class="row">';
                              location += '        <div class="col-xs-4 col-md-4">Geräte Verfolgung</div>';
                              location += '        <div id="zsld-tracking-location" class="col-xs-8 col-md-8">Wir konnten dein Gerät erflogreich lokalisieren</div>';
                              location += '    </div>';
                              $('#zsld-tracking-success').html(location);

                              var information = '';
                              information += '    <div class="row">';
                              information += '        <div class="col-xs-4 col-md-4">Accuracy </div>';
                              information += '        <div id="zsld-tracking-accuracy" class="col-xs-8 col-md-8"></div>';
                              information += '    </div>';
                              information += '    <div class="row">';
                              information += '        <div class="col-xs-4 col-md-4">Altitude </div>';
                              information += '        <div id="zsld-tracking-altitude" class="col-xs-8 col-md-8"></div>';
                              information += '    </div>';
                              information += '    <div class="row">';
                              information += '        <div class="col-xs-4 col-md-4">Altitude accuracy </div>';
                              information += '        <div id="zsld-tracking-altitudeAccuracy" class="col-xs-8 col-md-8"></div>';
                              information += '    </div>';
                              information += '    <div class="row">';
                              information += '        <div class="col-xs-4 col-md-4">Heading </div>';
                              information += '        <div id="zsld-tracking-heading" class="col-xs-8 col-md-4"></div>';
                              information += '    </div>';
                              information += '    <div class="row">';
                              information += '        <div class="col-xs-4 col-md-4">Speed </div>';
                              information += '        <div id="zsld-tracking-speed" class="col-xs-8 col-md-8"></div>';
                              information += '    </div>';
                              $("#zsld-tracking-information").append(information);

                              $http_location = new Rest(
                                function(response) {
                                  console.info('Device location notification successful ', response);
                                  passed('Device location notification  successful');

                                  var notifymc = '';
                                  notifymc += '    <div class="row">';
                                  notifymc += '        <div class="col-xs-4 col-md-4">Geräte Standort</div>';
                                  notifymc += '        <div id="zsld-tracking-location" class="col-xs-8 col-md-8">wurde ans KP gesendet</div>';
                                  notifymc += '    </div>';
                                  $('#zsld-tracking-success').html(notifymc);
                                },
                                function(data) {
                                  console.error('Error attempting to register your device ', data);
                                  failed('Error attempting to register your device');

                                  var notifymc = '';
                                  notifymc += '    <div class="row">';
                                  notifymc += '        <div class="col-xs-4 col-md-4">Geräte Standort</div>';
                                  notifymc += '        <div id="zsld-tracking-location" class="col-xs-8 col-md-8">konnte nicht ans KP gesendet werden</div>';
                                  notifymc += '    </div>';
                                  $('#zsld-tracking-error').html(notifymc);
                                }
                              );

                              zsld.GEOLOCATION = new ol.Geolocation({
                                // enableHighAccuracy must be set to true to have the heading value.
                                trackingOptions: {
                                  enableHighAccuracy: true
                                },
                                projection: zsld.MAP.getView().getProjection()
                              });

                              var tracking = '';
                              tracking += '<div id="zsld-tracking-check" class="form-check">';
                              tracking += '    <label for="zsld-tracking-check-label" class="form-check-label">Dein Standandort wird auf der Karte verfolgt </label>';
                              tracking += '    <input id="zsld-tracking-check-input" class="form-check-input" type="checkbox" value="trackme" ' + (TRACK_ME == '1' ? 'checked' : '') + '>';
                              tracking += '</div>';
                              $("#zsld-tracking-warning").append(tracking);

                              if (TRACK_ME == "1") {
                                zsld.GEOLOCATION.setTracking($('#zsld-tracking-check-input').is(':checked'));
                              }

                              $("#zsld-tracking-check-input").change(function() {
                                zsld.GEOLOCATION.setTracking($(this).is(':checked'));
                                if ($(this).is(':checked')) {
                                  $("#zsld-tracking-check-label").text('Dein Standandort wird auf der Karte verfolgt ');
                                  zsld.TRACK_ME = 'TRACKME=1; path=/; domain=zso-aargausued.ch; max-age=' + (60 * 60 * 24 * 365 * 100) + '; secure; samesite';
                                  document.cookie = zsld.TRACK_ME;
                                  TRACK_ME = zsld.getCookieValue('TRACKME');
                                } else {
                                  $("#zsld-tracking-check-label").text('Ich möchte meinen Standort auf der Karte verfolgen ');
                                  zsld.TRACK_ME = 'TRACKME=0; path=/; domain=zso-aargausued.ch; max-age=' + (60 * 60 * 24 * 365 * 100) + '; secure; samesite';
                                  document.cookie = zsld.TRACK_ME;
                                  TRACK_ME = zsld.getCookieValue('TRACKME');
                                }
                              });

                              var notification = '';
                              notification += '<div id="zsld-tracking-check-kp" class="form-check">';
                              notification += '    <label for="zsld-tracking-check-kp-label" class="form-check-label">Dein Standandort wird regelmässig dem KP mitgeteilt </label>';
                              notification += '    <input id="zsld-tracking-check-kp-input" class="form-check-input" type="checkbox" value="trackme" ' + (NOTIFY_MC == '1' ? 'checked' : '') + '>';
                              notification += '</div>';
                              $("#zsld-tracking-warning").append(notification);

                              $("#zsld-tracking-check-kp-input").change(function() {
                                if ($(this).is(':checked')) {
                                  $("#zsld-tracking-check-kp-label").text('Dein Standandort wird egelmässig dem KP mitgeteilt ');
                                  zsld.NOTIFY_MC = 'NOTIFYMC=1; path=/; domain=zso-aargausued.ch; max-age=' + (60 * 60 * 24 * 365 * 100) + '; secure; samesite';
                                  document.cookie = zsld.NOTIFY_MC;
                                  NOTIFY_MC = zsld.getCookieValue('NOTIFYMC');
                                } else {
                                  $("#zsld-tracking-check-kp-label").text('Ich möchte meinen Standort regelmässig dem KP mitteilen ');
                                  zsld.NOTIFY_MC = 'NOTIFYMC=0; path=/; domain=zso-aargausued.ch; max-age=' + (60 * 60 * 24 * 365 * 100) + '; secure; samesite';
                                  document.cookie = zsld.NOTIFY_MC;
                                  NOTIFY_MC = zsld.getCookieValue('NOTIFYMC');
                                }
                              });

                              /**
                              * Positioning information and tracking
                              * be carfull with this !!!
                              * @description send to kp if requested
                              *              POST https://zso-aargausued.ch/map/api/go?here=iam&id=0
                              *              BODY json string with location data
                              * @todo 
                              */
                              // implement http request
                              //
                              //

                              // update the map when the position changes
                              zsld.GEOLOCATION.on('change:position', function() {
                                var coordinates = zsld.GEOLOCATION.getPosition();
                                positionFeature.setGeometry(coordinates ? new ol.geom.Point(coordinates) : null);

                                var previous = [0, 0];
                                if (zsld.getCookieValue('COORDINATES')) {
                                  previous = JSON.parse(zsld.getCookieValue('COORDINATES'));
                                }

                                // implement timeing to send to the kp
                                if (NOTIFY_MC == "1" && ((previous[0] == 0 && previous[1] == 0) || (previous[0] + zsld.GEOLOCATION.getAccuracy() < coordinates[0] || previous[0] - zsld.GEOLOCATION.getAccuracy() > coordinates[0] || previous[1] + zsld.GEOLOCATION.getAccuracy() < coordinates[1] || previous[1] - zsld.GEOLOCATION.getAccuracy() > coordinates[1]))) {

                                  //if (previous[0] == 0 && previous[1] == 0) {
                                  zsld.COORDINATES = 'COORDINATES=' + JSON.stringify(coordinates) + '; path=/; domain=zso-aargausued.ch; max-age=' + (60 * 60 * 24 * 365 * 100) + '; secure; samesite';
                                  document.cookie = zsld.COORDINATES;
                                  //}

                                  var url = URL_TRACKING; //URL_REGISTRATION.replace(/(register=).*/, 'here=iam');
                                  //url += '&id=0';
                                  $http_location.post(url, JSON.stringify({
                                    "id": Math.trunc(coordinates[0]) + "</br>" + Math.trunc(coordinates[1]),
                                    "display": "position",
                                    "properties": {
                                      "accuracy": zsld.GEOLOCATION.getAccuracy() + ' [m]',
                                      "altitude": zsld.GEOLOCATION.getAltitude() + ' [m]',
                                      "altitudeAccuracy": zsld.GEOLOCATION.getAltitudeAccuracy() + ' [m]',
                                      "heading": zsld.GEOLOCATION.getHeading() + ' [rad]',
                                      "speed": zsld.GEOLOCATION.getSpeed() + ' [m/s]',
                                      "coordinates": coordinates
                                    },
                                    "forceconcurrentobjects": false,
                                    "concurrentobjectsallowed": false
                                  }));
                                }

                              });

                              // update the HTML page when the position changes
                              zsld.GEOLOCATION.on('change', function() {
                                $("#zsld-tracking-accuracy").text(zsld.GEOLOCATION.getAccuracy() + ' [m]');
                                $("#zsld-tracking-altitude").text(zsld.GEOLOCATION.getAltitude() + ' [m]');
                                $("#zsld-tracking-altitudeAccuracy").text(zsld.GEOLOCATION.getAltitudeAccuracy() + ' [m]');
                                $("#zsld-tracking-heading").text(zsld.GEOLOCATION.getHeading() + ' [rad]');
                                $("#zsld-tracking-speed").text(zsld.GEOLOCATION.getSpeed() + ' [m/s]');
                              });

                              // handle geolocation error
                              zsld.GEOLOCATION.on('error', function(error) {
                                $('#zsld-tracking-error').text(error.message);
                                $('#zsld-tracking-error').show();
                              });


                              /**
                              * 
                              */
                              var accuracyFeature = new ol.Feature();
                              zsld.GEOLOCATION.on('change:accuracyGeometry', function() {
                                accuracyFeature.setGeometry(zsld.GEOLOCATION.getAccuracyGeometry());
                              });

                              var positionFeature = new ol.Feature();
                              positionFeature.setStyle(new ol.style.Style({
                                image: new ol.style.Circle({
                                  radius: 16,
                                  fill: new ol.style.Fill({
                                    color: '#00EF39'
                                  }),
                                  stroke: new ol.style.Stroke({
                                    color: '#FF5208',
                                    width: 8
                                  })
                                })
                              }));

                              /**
                              * 
                              */
                              // new ol.layer.Vector({
                              //   map: map,
                              //   source: new ol.source.Vector({
                              //     features: [accuracyFeature, positionFeature]
                              //   })
                              // });
                              zsld.VECTORS.add('DEVICE_TRACKING').addFeatures([accuracyFeature, positionFeature], {
                                clear: true,
                                activate: true,
                                append: false,
                                overwrite: true
                              });
                            },
                            function(position) {
                              console.error('Error attempting to retrieve your location ', position);
                              failed('Error attempting to retrieve your location');
                              //$("#zsld-tracking-locating").remove();
                              //var status = '<div id="zsld-tracking-location" class="alert alert-warning fade in" role="alert">Error attempting to retrieve your location</div>';
                              //$("#zsld-tracking-status").append(status);
                              var location = '';
                              location += '    <div class="row">';
                              location += '        <div class="col-xs-4 col-md-4">Device tracking</div>';
                              location += '        <div id="zsld-tracking-location" class="col-xs-8 col-md-8">Error attempting to retrieve your location</div>';
                              location += '    </div>';
                              $('#zsld-tracking-error').html(location);
                            });

                        } else {
                          console.error('Geolocation is not supported by your browser');
                          failed('Geolocation is not supported by your browser');
                          //var status = '<div id="zsld-tracking-locating" class="alert alert-warning fade in" role="alert">Geolocation is not supported by your browser</div>';
                          //$("#zsld-tracking-status").append(status);
                          var locating = '';
                          locating += '    <div class="row">';
                          locating += '        <div class="col-xs-4 col-md-4">Geolocation service</div>';
                          locating += '        <div id="zsld-tracking-locating" class="col-xs-8 col-md-8">Geolocation is not supported by your browser</div>';
                          locating += '    </div>';
                          $('#zsld-tracking-error').html(locating);
                        }
                      };

                      var initializeForm = function() {
                        var tracking_error = $('<div id="zsld-tracking-error" class="alert alert-danger fade in" role="alert"></div>');
                        tracking_error.hide();
                        $("#zsld-tracking-settings").append(tracking_error);

                        var tracking_warning = '<div id="zsld-tracking-warning" class="alert alert-warning fade in" role="alert"></div>';
                        $("#zsld-tracking-settings").append(tracking_warning);

                        var tracking_success = '<div id="zsld-tracking-success" class="alert alert-success fade in" role="alert"></div>';
                        $("#zsld-tracking-settings").append(tracking_success);

                        var tracking_information = '<div id="zsld-tracking-information" class="alert alert-info fade in" role="alert"></div>';
                        $("#zsld-tracking-settings").append(tracking_information);

                        // decoder
                        zsld._decode = function(s) {
                          return parseInt('0x' + s.split("").reverse().join(""));
                        }

                        // Check if Registration is declined
                        zsld.getCookieValue = function(a) {
                          const b = document.cookie.match('(^|;)\\s*' + a + '\\s*=\\s*([^;]+)');
                          return b ? b.pop() : '';
                        }
                        zsld.REGISTRATION_PROHIBITED = 'REGISTRATIONPROHIBITED=1; path=/; domain=zso-aargausued.ch; max-age=' + (60 * 60 * 24 * 365 * 100) + '; secure; samesite';
                        REGISTRATION_PROHIBITED = zsld.getCookieValue('REGISTRATIONPROHIBITED');

                        // Get Tracking flags
                        zsld.TRACK_ME = 'TRACKME=1; path=/; domain=zso-aargausued.ch; max-age=' + (60 * 60 * 24 * 365 * 100) + '; secure; samesite';
                        TRACK_ME = zsld.getCookieValue('TRACKME');
                        if (!TRACK_ME) {
                          document.cookie = zsld.TRACK_ME;
                          TRACK_ME = zsld.getCookieValue('TRACKME');
                        }
                        zsld.NOTIFY_MC = 'NOTIFYMC=1; path=/; domain=zso-aargausued.ch; max-age=' + (60 * 60 * 24 * 365 * 100) + '; secure; samesite';
                        NOTIFY_MC = zsld.getCookieValue('NOTIFYMC');
                        if (!NOTIFY_MC) {
                          document.cookie = zsld.NOTIFY_MC;
                          NOTIFY_MC = zsld.getCookieValue('NOTIFYMC');
                        }

                        REGISTRATIONDIALOG = zsld.getCookieValue('REGISTRATIONDIALOG');
                        console.debug("REGISTRATIONDIALOG ", REGISTRATIONDIALOG, zsld.getCookieValue('REGISTRATIONDIALOG'));

                        deviceTracking();

                        deviceRegistration();

                        if (REGISTRATIONDIALOG || (QUERY_STRING.tic && REGISTERED_DEVICE > -2 && REGISTERED_DEVICE < 1) || (REGISTERED_DEVICE > -2 && REGISTERED_DEVICE < 1 && REGISTRATION_PROHIBITED != "1")) {
                          if (REGISTRATIONDIALOG || QUERY_STRING.tic || REGISTERED_DEVICE != 0) {
                            $('#REGISTRATION_PROHIBITED_BUTTON').hide();
                          }
                          $('#modal-tracking').modal('show');
                        }
                      };

                      initializeForm();
                    </script>
                <?php
                } else {
                    ?>
                    <script>$('#REGISTRATION_PROHIBITED_BUTTON').hide();</script>
                    Du hast zu lange gezögert um dich zu registrieren, dein Zugriff wurde gesperrt. Bitte melde dich im KP damit dein Zugriff wieder freigeschalten werden kann. 
                    <?php
                } 
                ?> 
                </div>
            </div>
        </div>
        <!-- /#wrapper -->

    </body>

</html>
<?php
}
?>