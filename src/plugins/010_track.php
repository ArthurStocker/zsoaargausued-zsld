<!-- partial:tracking -->
<span id="nav-container-tracking">
    <!-- Button trigger modal -->
    <button id="nav-item-tracking" type="button" class="btn btn-primary btn-lg modal-btn" data-toggle="modal" data-target="#modal-tracking">
        <i class="fab fa-whmcs fa-1x" aria-hidden="true"></i>
    </button>

    <!-- Modal -->
    <div id="modal-tracking" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-label-tracking" aria-hidden="true">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="modal-close ga-btn close" data-dismiss="modal">
                        <i class="fa fa-times fa-1x" aria-hidden="true"></i>
                    </button>
                    <h4 id="modal-label-tracking" class="modal-title">Geräteregistration</h4>
                </div>

                <div class="modal-body">
                    <div id="tracking-container" lass="form-horizontal collapse in" aria-expanded="true" style="">

                        <div id="zsld-tracking-settings" class="form-group"></div>

                    </div>
                </div>

                <div class="modal-footer">
                    <div class="btn-group">
                        <button class="btn btn-default" data-dismiss="modal">Schliessen</button>
                    </div>
                    <div class="btn-group">
                        <button id="REGISTRATION_PROHIBITED_BUTTON" class="btn btn-danger" data-dismiss="modal" onclick="document.cookie = zsld.REGISTRATION_PROHIBITED;">Nein danke!</button>
                    </div>
                </div>
                
            </div>

        </div>
    </div>

</span>
