<!-- partial:geojson -->
<span id="nav-container-geojson">
    <!-- Button trigger modal -->
    <button id="nav-item-geojson" type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modal-geojson">
        <i class="fa fa-sticky-note fa-1x" aria-hidden="true"></i>
    </button>

    <!-- Modal -->
    <div id="modal-geojson" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-label-geojson" aria-hidden="true">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="modal-close ga-btn close" data-dismiss="modal">
                        <i class="fa fa-times fa-1x" aria-hidden="true"></i>
                    </button>
                    <h4 id="modal-label-geojson" class="modal-title">Load GeoJSON</h4>
                </div>

                <div class="modal-body">
                    <div id="geojson-container" lass="form-horizontal collapse in" aria-expanded="true" style="">

                        <div class="form-group">
                            <label>Features</label>
                            <div class="alert alert-info">Url to the features file to be loaded.</div>
                            <input name="feature-url" class="form-control"
                                placeholder="Enter a GeoJSON file url ..."
                                onClick="this.select();"
                                value="<?php echo URL_GEOJSON_FEATURE ?>">
                        </div>
                        <div class="form-group">
                            <label>Style</label>
                            <div class="alert alert-info">Url to the styling file to be loaded.</div>
                            <div class="input-group">
                                <input name="style-url" class="form-control"
                                    placeholder="Enter a styling JSON file url ..."
                                    onClick="this.select();"
                                    value="<?php echo URL_GEOJSON_STYLE ?>">
                                <span class="input-group-btn">
                                <button type="button" class="btn btn-default" type="button" onclick="geojson_loadStyles(true)">Load styling</button>
                                </span>
                            </div>
                            <form id="editor-form" name="editor-form" style="padding: 10px 0px 10px 0px">
                                <textarea id="editor-content" name="editor-content" class="form-control"
                                     placeholder="GeoJSON styling editor ..."
                                    spellcheck="false">
                                </textarea>
                            </form>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default" onclick="geojson_applyStyling()">Apply styling</button>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default" onclick="geojson_removeLayer()">Remove layer</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="geojson_addLayer()">Add layer</button>
                    </div>
                </div>
                
            </div>

        </div>
    </div>

    <script>
        // Load styles
        var geojson_loadStyles = function(editor_only) {
            $http = new Rest(
                function(data) {
                    console.info('Styles successfully loaded ', data);
                    if (!editor_only) zsld.VECTORS.add('GEOJSON').setStyles(data);
                    $('#editor-content').text(JSON.stringify(data, null, 4));
                    passed('Styles successfully loaded');
                },
                function(data) {
                    console.error('Error attempting to load styles ', data);
                    $('#editor-content').text('');
                    failed('Error attempting to load styles');
                }
            );
            $http.get(getUrl('style-url'));
        };

        // Apply style changes from editor
        var geojson_applyStyling = function() {
            var data = JSON.parse($('#editor-content').val());
            zsld.VECTORS.add('GEOJSON').setStyles(data);
            loadFeatures();
        };

        // Load features
        var geojson_loadFeatures = function() {
            $http = new Rest(
                function(data) {
                    console.info('Features successfully loaded ', data);
                    zsld.VECTORS.add('GEOJSON').addFeatures(zsld.GEOJSONPARSER.readFeatures(data), { clear: true, activate: true, append:true, overwrite: false });
                    passed('Features successfully loaded');
                },
                function(data) {
                    console.error('Error attempting to load features ', data);
                    zsld.VECTORS.remove('GEOJSON', true, true);
                    failed('Error attempting to load features');
                }
            );
            $http.get(getUrl('feature-url'));
        };

        // Remove vector layer from map
        var geojson_removeLayer = function() {
            zsld.VECTORS.remove('GEOJSON', true, true);
        };

        // Apply GeoJSON config from urls
        var geojson_addLayer = function() {
            geojson_loadStyles();
            geojson_loadFeatures();
        }
    </script>

</span>