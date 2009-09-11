function bubblecastHideElement(elem) {
    elem.style.display = 'none';
}

function bubblecastShowElement(elem) {
    elem.style.display = 'block';
}

function bubblecastChangeFlashesVisibility(visible, idToIgnore) {
	var els = document.getElementsByTagName('object');
	for (var i = 0; i < els.length; i++) {
	    var el = els[i];
        var elToCheck = el;
        var found = false;
        while (elToCheck.parentNode != null && idToIgnore != null) {
            if (elToCheck.id == idToIgnore) {
                found = true;
                break;
            }
            elToCheck = elToCheck.parentNode;
        }
        if (!found || idToIgnore == null) {
            el.style.display = visible ? 'block' : 'none';
        }
	}
}

function bubblecastShowFlashes() {
	bubblecastChangeFlashesVisibility(true, null);
}

function bubblecastHideFlashes(idToIgnore) {
	bubblecastChangeFlashesVisibility(false, idToIgnore);
}

function bubblecastPositionElementAtScreenCenter(elem) {
    var windowWidth;
    var windowHeight;
    if (window.innerWidth) {
        windowWidth = window.innerWidth;
        windowHeight = window.innerHeight;
    } else if (document.documentElement.clientWidth) {
        windowWidth = document.documentElement.clientWidth;
        windowHeight = document.documentElement.clientHeight;
    } else {
        windowWidth = document.body.clientWidth;
        windowHeight = document.body.clientHeight;
    }
    var left = Math.round((windowWidth - elem.offsetWidth) / 2);
    var top = Math.round((windowHeight - elem.offsetHeight) / 2);
    if (left < 0) left = 0;
    if (top < 0) top = 0;
    left += Math.max(document.documentElement.scrollLeft, document.body.scrollLeft);
    top += Math.max(document.documentElement.scrollTop, document.body.scrollTop);
    elem.style.left = left + 'px';
    elem.style.top = top + 'px';
}

function showBubblecastComment() {
    var elem = document.getElementById('bubblecast_comment');
    // moving element to the top level in the hierarchy to avoid clipping in
    // some themes
    elem.parentNode.removeChild(elem);
    document.body.appendChild(elem);

    bubblecastHideFlashes('bubblecast_comment');
    bubblecastShowElement(elem);
    bubblecastPositionElementAtScreenCenter(elem);
}

function hideBubblecastComment() {
    var elem = document.getElementById('bubblecast_comment');
    bubblecastHideElement(elem);
    bubblecastShowFlashes();
}

function insertAtCaret(doc, areaId, formId, areaName, text) {
    var txtarea;
    // first trying to find by form, element name
    var form = doc.getElementById(formId);
    if (form && form.tagName == 'FORM') {
        txtarea = form.elements[areaName];
    }
    if (!txtarea) {
	// falling back to ID
        txtarea = doc.getElementById(areaId);
    }
    
    var scrollPos = txtarea.scrollTop;
    var strPos = 0;
    var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? "ff" : (doc.selection ? "ie" : false ) );
    if (br == "ie") {
        txtarea.focus();
        var range = doc.selection.createRange();
        range.moveStart('character', -txtarea.value.length);
        strPos = range.text.length;
    } else if (br == "ff") strPos = txtarea.selectionStart;
    var front = (txtarea.value).substring(0, strPos);
    var back = (txtarea.value).substring(strPos, txtarea.value.length);
    txtarea.value = front + text + back;
    strPos = strPos + text.length;
    if (br == "ie") {
        txtarea.focus();
        var range = doc.selection.createRange();
        range.moveStart('character', -txtarea.value.length);
        range.moveStart('character', strPos);
        range.moveEnd('character', 0);
        range.select();
    } else if (br == "ff") {
        txtarea.selectionStart = strPos;
        txtarea.selectionEnd = strPos;
        txtarea.focus();
    }
    txtarea.scrollTop = scrollPos;
}
