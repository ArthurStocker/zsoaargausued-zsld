<!-- partial:geojson -->
<span id="nav-container-geojson">
    <!-- Button trigger modal -->
    <button id="nav-item-geojson" type="button" class="btn btn-primary btn-lg modal-btn" data-toggle="modal" data-target="#modal-geojson">
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
                                <button type="button" class="btn btn-default" type="button" onclick="Plugins.geojson.loadStyles(true)">Load styling</button>
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
                            <button type="button" class="btn btn-default" onclick="Plugins.geojson.applyStyling()">Apply styling</button>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Schliessen</button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="Plugins.geojson.removeLayer()">Karte entfernen</button>
                        <button type="button" class="btn btn-success" data-dismiss="modal" onclick="Plugins.geojson.addLayer()">Karte hinzufügen</button>
                    </div>
                </div>
                
            </div>

        </div>
    </div>

</span>