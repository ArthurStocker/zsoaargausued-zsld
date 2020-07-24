<!-- partial:reports -->
<span id="nav-container-reports">
    <!-- Button trigger modal -->
    <button id="nav-item-reports" type="button" class="btn btn-primary btn-lg modal-btn" data-toggle="modal" data-target="#modal-reports">
        <i class="far fa-file-alt fa-1x" aria-hidden="true"></i>
    </button>

    <!-- Modal -->
    <div id="modal-reports" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-label-reports" aria-hidden="true">
        <div class="modal-dialog">

            <div class="modal-content">
                <!--
                <form id="zsld-reports-form" action="" method="GET">
                -->
                    <div class="modal-header">
                        <button type="button" class="modal-close ga-btn close" data-dismiss="modal">
                            <i class="fa fa-times fa-1x" aria-hidden="true"></i>
                        </button>
                        <h4 id="modal-label-reports" class="modal-title">Bericht</h4>
                    </div>

                    <div class="modal-body">
                        <div id="reports-container" lass="form-horizontal collapse in" aria-expanded="true" style="">
                            <div id="zsld-reports" class="form-group">
                                <a href="/map/api/report?format=csv&transaction=fahrzeuge&objectstore=devices" download="bericht.csv">Den Bericht als CSV Datei herunterladen</a></br>
                                <a href="/map/api/report?format=xlsx&transaction=fahrzeuge&objectstore=devices" download="bericht.xlsx">Den Bericht als Excel Datei herunterladen</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <div class="btn-group">
                            <button id="zsld-reports-button-close" class="btn btn-default" data-dismiss="modal">Schliessen</button>
                            <!--
                            <button id="zsld-reports-button-cancel" class="btn btn-danger" data-dismiss="modal">Abbrechen</button>
                            <button id="zsld-reports-button-ok" class="btn btn-success" data-dismiss="modal">Ok</button>
                            -->
                        </div>
                    </div>
                <!--    
                </form>
                -->
            </div>

        </div>
    </div>

</span>