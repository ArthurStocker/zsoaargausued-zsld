<!-- partial:search -->
<span id="nav-container-lookup">
    <!-- Button trigger modal -->
    <button id="nav-item-lookup" type="button" class="btn btn-primary btn-lg modal-btn" data-toggle="modal" data-target="#modal-lookup">
        <i class="fa fa-map-marked-alt fa-1x" aria-hidden="true"></i>
    </button>

    <!-- Modal -->
    <div id="modal-lookup" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-label-lookup" aria-hidden="true">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="modal-close ga-btn close" data-dismiss="modal">
                        <i class="fa fa-times fa-1x" aria-hidden="true"></i>
                    </button>
                    <h4 id="modal-label-lookup" class="modal-title">Suche nach PLZ, Ort oder Adresse</h4>
                </div>

                <div class="modal-body">
                    <div id="lookup-container" ga-search class="form-horizontal collapse in" aria-expanded="true" style="">

                        <div class="form-group" ga-condition ga-index="1">
                            <div ga-header class="col-md-12">
                                <label>Gesuchter Text</label>
                            </div>
                            <div class="col-md-12" ga-body>
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <b>PLZ:</b> Für «3007 in Bern» tippe <i>3007</i><br>
                                        <b>Ort:</b> Für «Rue de l'Evêché in Genf» tippe <i>évêch</i><br>
                                    </div>
                                    <input name="feature-yha-url" class="form-control hidden"
                                        placeholder="Enter a GeoJSON file url ..."
                                        onClick="this.select();"
                                        value="<?php echo URL_GEOJSON_FEATURE_YAH ?>">
                                </div>
                                <div class="col-md-12">
                                    <input id="search" type="text" class="form-control typeahead" data-provide="typeahead" style="border-radius: 4px"
                                        title="For «3007 in Bern» tap '3007', For «Rue de l'Evêché in Genf» tap 'évêch'  ..."
                                        placeholder="Search city, zip  ...">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Schliessen</button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="Plugins.search.removeLayer()">Karte entfernen</button>
                        <button type="button" class="btn btn-success" data-dismiss="modal" >Karte hinzufügen</button>
                    </div>
                </div>

            </div>

        </div>
    </div>

</span>