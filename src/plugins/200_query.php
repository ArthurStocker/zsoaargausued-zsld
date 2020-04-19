<!-- partial:query -->
<span id="nav-container-query">
    <!-- Button trigger modal -->
    <button id="nav-item-query" type="button" class="btn btn-primary btn-lg modal-btn" data-toggle="modal" data-target="#modal-query">
        <i class="fa fa-city fa-1x" aria-hidden="true"></i>
    </button>

    <!-- Modal -->
    <div id="modal-query" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-label-query" aria-hidden="true">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="modal-close ga-btn close" data-dismiss="modal">
                        <i class="fa fa-times fa-1x" aria-hidden="true"></i>
                    </button>
                    <h4 id="modal-label-query" class="modal-title">Suche nach Gemeindename oder BFS-Nummer</h4>
                </div>

                <div class="modal-body">
                    <div id="query-container" ga-query class="collapse in" aria-expanded="true" style="">

                        <div class="form-group" ga-condition ga-index="1">
                            <div class="row">
                                <div ga-header class="col-md-12">
                                    <label>Bedingung Nr. 1</label>
                                    <button class="ga-icon ga-btn fa fa-times pull-right query-option-icon" title="Bedingung entfernen" onclick=removeCondition(this)></button>
                                    <button class="ga-icon ga-btn fa fa-redo pull-right query-option-icon" title="Bedingung zurücksetzen" onclick=resetCondition(this)></button>
                                    <button class="ga-icon ga-btn fa fa-copy pull-right query-option-icon" title="Bedingung duplizieren" onclick=copyCondition(this)></button>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-8">
                                        <select class="form-control" ga-term="field:names" ga-selectedIndex="0" onchange=selectChange(this)>
                                            <option label="Name" value="object:8321" selected>Name</option>
                                            <option label="BFS-Nummer" value="object:8322">BFS-Nummer</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" ga-term="object:8321" ga-selectedIndex="0" onchange=selectChange(this)>
                                            <option label="ilike" value="string:ilike" selected>ilike</option>
                                            <option label="not ilike" value="string:not ilike">not ilike</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" style="border-radius: 4px" ga-term="object:8321"
                                            title="epalinges, ependes (vd), grub (ar), leuk, uesslingen-buch ..."
                                            placeholder="epalinges, ependes (vd), grub  ...">
                                    </div>
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
                        <button type="button" class="btn btn-success" data-dismiss="modal" onclick=execQuery()>Abfragen</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

</span>