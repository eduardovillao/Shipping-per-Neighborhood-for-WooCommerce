function wsnNewRow(e) {

    let lastItem = document.querySelector('.wsn-table__body');
    
    let rowIndex = parseInt(lastItem.lastElementChild.dataset.index) + parseInt(1);
    let svg = document.querySelector('.wsn-table__action > svg').outerHTML;
    let template = '<tr class="wsn-table__row" data-index="' + rowIndex + '"><td><input type="text" name="woocommerce_woo_shipping_per_neighborhood_wsn_repeater_city[' + rowIndex + ']" class="regular-text" style="width: 100%;" value=""> </td><td><input type="text" name="woocommerce_woo_shipping_per_neighborhood_wsn_repeater_neighborhood[' + rowIndex + ']" class="regular-text" style="width: 100%;" value=""></td><td> <input type="number" name="woocommerce_woo_shipping_per_neighborhood_wsn_repeater_neighborhood_price[' + rowIndex + ']" class="regular-text" style="width: 100%;" value=""></td><td class="wsn-table__action" onclick="wsnRemoveRow(event)">' + svg + '</td></tr>';

    lastItem.insertAdjacentHTML('beforeend', template);
}

function wsnRemoveRow(e) {
    let row = e.target.closest('.wsn-table__row');
    if(row.previousElementSibling) {
		row.remove();
	}
}