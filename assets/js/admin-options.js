const add = document.querySelector('.wsn-table__add');
const tableBody = document.querySelector('.wsn-table__body');

if(add != undefined) {
    add.addEventListener('click', (event) => {
        tableBody.insertAdjacentHTML('beforeend', newRow(tableBody.lastElementChild.dataset.index));
    } );
}

if(tableBody != undefined) {
    tableBody.addEventListener( 'click', (event) => {
        if( event.target.parentElement.nodeName == 'svg' && event.target.parentElement.matches('.wsn-table__remove') ) {
            event.target.closest(".wsn-table__row").remove()
        }
    } );
}

function newRow(index) {
    
    const rowIndex = parseInt(index) + parseInt(1);
    const svg = document.querySelector('.wsn-table__action > svg').outerHTML;
    return '<tr class="wsn-table__row" data-index="' + rowIndex + '"><td><input type="text" name="woocommerce_woo_shipping_per_neighborhood_wsn_repeater_city[' + rowIndex + ']" class="regular-text" style="width: 100%;" value=""> </td><td><input type="text" name="woocommerce_woo_shipping_per_neighborhood_wsn_repeater_neighborhood[' + rowIndex + ']" class="regular-text" style="width: 100%;" value=""></td><td> <input type="number" name="woocommerce_woo_shipping_per_neighborhood_wsn_repeater_neighborhood_price[' + rowIndex + ']" class="regular-text" style="width: 100%;" value=""></td><td class="wsn-table__action" style="width: 10%; text-align: center; vertical-align: middle;">' + svg + '</td></tr>';
}