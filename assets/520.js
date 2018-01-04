jQuery.component = function(tagname, settings) {
	var proto = Object.create(HTMLElement.prototype);
	return document.registerElement(tagname, {prototype:proto});
};