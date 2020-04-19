<!-- partial:transactions -->
<span id="nav-container-transactions">
    <!-- Button trigger modal -->
    <button id="nav-item-transactions" type="button" class="btn btn-primary btn-lg modal-btn" data-toggle="modal" data-target="#modal-transactions">
        <i class="fab fa-whmcs fa-1x" aria-hidden="true"></i>
    </button>

    <!-- Modal -->
    <div id="modal-transactions" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-label-transactions" aria-hidden="true">
        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="modal-close ga-btn close" data-dismiss="modal">
                        <i class="fa fa-times fa-1x" aria-hidden="true"></i>
                    </button>
                    <h4 id="modal-label-transactions" class="modal-title">Transaktionen</h4>
                </div>

                <div class="modal-body">
                    <div id="transactions-container" lass="form-horizontal collapse in" aria-expanded="true" style="">

                        <div id="zsld-transactions" class="form-group"></div>
                        <!-- Nav tabs -->
                        <ul id="zsld-transaction-tab-nav" class="nav nav-tabs" role="tablist"></ul>
                        <!-- Tab panes -->
                        <div id="zsld-transaction-tab-pane" class="tab-content"></div>

                    </div>
                </div>

                <div class="modal-footer">
                    <div class="btn-group">
                        <button class="btn btn-default" data-dismiss="modal">Schliessen</button>
                    </div>
                </div>
                
            </div>

        </div>
    </div>

</span>