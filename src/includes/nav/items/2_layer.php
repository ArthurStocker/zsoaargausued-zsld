<!-- partial:layer -->
<span id="nav-container-layer">
    <!-- Button trigger modal -->
    <button id="nav-item-layer" type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modal-layer">
        <i class="fas fa-layer-group fa-1x" aria-hidden="true"></i>
    </button>

    <!-- Modal -->
    <div id="modal-layer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-label-layer" aria-hidden="true">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="modal-close ga-btn close" data-dismiss="modal">
                        <i class="fa fa-times fa-1x" aria-hidden="true"></i>
                    </button>
                    <h4 id="modal-label-layer" class="modal-title">Layers</h4>
                </div>

                <div class="modal-body">
                    <div id="layer-container" lass="form-horizontal collapse in" aria-expanded="true" style="">

                        <div id="zsld-layers" class="form-group">

                        </div>
                        
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="btn-group">
                        <button class="btn btn-primary" onclick=loadFeatures()>Display</button>
                    </div>
                </div>
                
            </div>

        </div>
    </div>

</span>