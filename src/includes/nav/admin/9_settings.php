<!-- partial:settings -->
<span id="nav-container-settings">
    <!-- Button trigger modal -->
    <button id="nav-item-settings" type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modal-settings">
        <i class="fab fa-whmcs fa-1x" aria-hidden="true"></i>
    </button>

    <!-- Modal -->
    <div id="modal-settings" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-label-settings" aria-hidden="true">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="modal-close ga-btn close" data-dismiss="modal">
                        <i class="fa fa-times fa-1x" aria-hidden="true"></i>
                    </button>
                    <h4 id="modal-label-settings" class="modal-title">Settings</h4>
                </div>

                <div class="modal-body">
                    <div id="settings-container" lass="form-horizontal collapse in" aria-expanded="true" style="">

                        <div class="form-group">
                            <label>Layers</label>

                        </div>
                        <div class="form-group">
                            <label>Styles</label>


                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="btn-group">
                        <button class="btn btn-primary" onclick=saveSettings()>Save</button>
                    </div>
                </div>
                
            </div>

        </div>
    </div>

</span>