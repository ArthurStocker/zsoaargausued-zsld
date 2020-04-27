<!-- partial:login -->
<span id="nav-container-login">
    <!-- Button trigger modal -->
    <button id="nav-item-login" type="button" class="btn btn-primary btn-lg modal-btn" data-toggle="modal" data-target="#modal-login">
        <i class="fas fa-sign-in-alt fa-1x" aria-hidden="true"></i>
    </button>

    <!-- Modal -->
    <div id="modal-login" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-label-login" aria-hidden="true">
        <div class="modal-dialog">

            <div class="modal-content">
                <form id="zsld-login-form" action="" method="POST">

                    <div class="modal-header">
                        <button type="button" class="modal-close ga-btn close" data-dismiss="modal">
                            <i class="fa fa-times fa-1x" aria-hidden="true"></i>
                        </button>
                        <h4 id="modal-label-login" class="modal-title">Login</h4>
                    </div>

                    <div class="modal-body">
                        <div id="login-container" lass="form-horizontal collapse in" aria-expanded="true" style="">
                            <div id="zsld-login" class="form-group">

                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="btn-group">
                            <button id="zsld-login-button-cancel" class="btn btn-danger" data-dismiss="modal">Abbrechen</button>
                            <button id="zsld-login-button-ok" class="btn btn-success" data-dismiss="modal">Ok</button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>

</span>