/**
 * Transaction lists
 * to show the details of a single transaction
 * we have to implement a stacked modal feature
 * and a button column to trigger the detail modal.
 * 
 * 
 * Stacked Bootstrap Modal Example
 * http://jsfiddle.net/CxdUQ/
 * 
 */
// create plugin
if (new Plugins('transaction')) {

  // Load Transactions
  Plugins.transaction.transactionStores = function() {
    $http_transaction = new Rest(
      function(data) {
        console.info('Transaction stores successfully loaded ', data);
        if (!$("#zsld-transactions-title").length) {
          var title = '<label id="zsld-transactions-title">Details zu den Transaktionen</label>';
          $("#zsld-transactions").append(title);
        }
        for (var i = 0; i < data.stores.length; i++) {
          var url = URL_TRANSACTIONS.replace(/(type=)[^&]*/, '$1transaction');
          url = url.replace(/(object=)[^&]*/, '$1' + data.stores[i].name);
          var all_url = url + '&id=-1';
          var last_url = url + '&id=0';
          console.debug('Transaction store URL for ' + data.stores[i].name, last_url);
          if (!$("#zsld-transactions-store-" + data.stores[i].name).length) {
            var tabs = '';
            tabs += '<li role="presentation" ' + (i == 0 ? 'class="active"' : '') + '>';
            tabs += '<a id="zsld-transaction-action-' + data.stores[i].name + '" href="#zsld-transaction-tab-' + data.stores[i].name + '" ' + (i == 0 ? 'class="active"' : '') + ' aria-controls="zsld-transaction-tab-' + data.stores[i].name + '" role="tab" data-toggle="tab"  data-list="zsld-transaction-list-' + data.stores[i].name + '" data-url="' + last_url + '">' + data.stores[i].name.charAt(0).toUpperCase() + data.stores[i].name.slice(1) + '</a>';
            tabs += '</li>';
            $("#zsld-transaction-tab-nav").append(tabs);
            var files = '';
            files += '<div id="zsld-transaction-tab-' + data.stores[i].name + '" role="tabpanel" class="tab-pane ' + (i == 0 ? 'active' : '') + '">';
            files += '    <div id="zsld-transactions-store-' + data.stores[i].name + '" class="form-check">';
            files += '        <label id="zsld-transaction-title-' + data.stores[i].name + '" class="form-check-label" >'; // + data.stores[i].name.charAt(0).toUpperCase() + data.stores[i].name.slice(1)
            files += '            <div class="btn-group btn-group-xs" role="group" aria-label="...">';
            files += '                <button id="zsld-transaction-switch-' + data.stores[i].name + '-last" type="button" class="btn btn-default" data-list="zsld-transaction-list-' + data.stores[i].name + '" data-url="' + last_url + '">Letzte Bewegungen</button>';
            files += '                <button id="zsld-transaction-switch-' + data.stores[i].name + '-all" type="button" class="btn btn-default" data-list="zsld-transaction-list-' + data.stores[i].name + '" data-url="' + all_url + '">Alle Bewegungen</button>';
            files += '            </div>';
            files += '        </label>';
            files += '        <table id="zsld-transaction-list-' + data.stores[i].name + '" class="table table-striped table-bordered" style="width:100%"></table>';
            files += '    </div>';
            files += '</div>';
            $("#zsld-transaction-tab-pane").append(files);
            $('#zsld-transaction-switch-' + data.stores[i].name + '-last').click(Plugins.transaction.resetList);
            $('#zsld-transaction-switch-' + data.stores[i].name + '-all').click(Plugins.transaction.resetList);
            $('#zsld-transaction-action-' + data.stores[i].name).click(Plugins.transaction.resetList);
            $("#zsld-transaction-list-" + data.stores[i].name).DataTable({
              ajax: {
                url: last_url,
                dataSrc: 'transactions'
              },
              columns: [
                { title: 'ID', data: 'id' },
                /*{ data: 'type' },*/
                { title: 'Status', data: 'data' },
                /*{ data: 'valid' },*/
                { title: 'Bestätigt', data: 'decision' },
                /*{ data: 'transaction' },*/
                { title: 'Transaktion', data: 'tic' }
              ],
              buttons: [
                'searchPanes'
              ],
              dom: 'Bfrt<"tbl-right"p><"tbl-right-p50"l>i'
            });
          }
        }
        passed('Transaction stores successfully loaded');
      },
      function(data) {
        console.error('Error attempting to load Transaction stores ', data);
        failed('Error attempting to load Transaction stores');
      }
    );
    $http_transaction.get(URL_TRANSACTIONS);
  }

  // reload list with respective data
  // on model shown
  $('#modal-transactions').on('shown.bs.modal', function(e) {
    $("#zsld-transaction-tab-nav a").each(Plugins.transaction.resetList);
  });
  // click the list switches
  Plugins.transaction.resetList = function(e) {
    $('#' + $(this).data('list')).DataTable().ajax.url($(this).data('url')).load();
  }
}

Plugins.transaction.transactionStores();
