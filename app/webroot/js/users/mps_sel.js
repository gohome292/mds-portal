function optAdd(to, option, inx) {
	if (inx==to.length) {
		try {
			 to.add(option);
		} catch(ex) {
			 to.add(option);
		}
	} else {
		 try {
	 		to.add(option, inx);
 		 } catch(ex) {
			var last = to.options[inx];
			to.add(option, last);
		 }
	}
}

//左右移動用
function onSelectObj(from, to) {
	var inx=to.length;
	for(var i = from.length - 1; i>=0; i--) {
		var option = from.options[i];
		if(option.selected) {
			from.remove(i);
			optAdd(to, option, inx);
		}
	}
}
//サブミット前
function makeCustSel() {
	var mps_customer = "";
	var cust_sel = document.getElementById("cust_sel");
	for(var i = 0; i<cust_sel.length; i++) {
		var option = cust_sel.options[i];
		mps_customer += "|" + option.value;
	}
	if (mps_customer!="")
		mps_customer=mps_customer.substr(1);
	document.getElementById("UserMpsCustomerId").value=mps_customer;
}
