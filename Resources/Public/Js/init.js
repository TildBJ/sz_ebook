var pdfWithFormsPath = jQuery('#viewer').data('pdf');

PDFJS.workerSrc = '/typo3conf/ext/sz_ebook/Resources/Public/Js/pdf/worker_loader.js';
/* -*- Mode: Java; tab-width: 2; indent-tabs-mode: nil; c-basic-offset: 2 -*- */
/* vim: set shiftwidth=2 tabstop=2 autoindent cindent expandtab: */

//
// Basic AcroForms input controls rendering
//

'use strict';

var formFields = {};

function setupForm(div, content, viewport) {
  function bindInputItem(input, item) {
    if (input.name in formFields) {
      var value = formFields[input.name];
      if (input.type == 'checkbox') {
        input.checked = value;
      } else if (!input.type || input.type == 'text') {
        input.value = value;
      }
    }
    input.onchange = function pageViewSetupInputOnBlur() {
      if (input.type == 'checkbox') {
        formFields[input.name] = input.checked;
      } else if (!input.type || input.type == 'text') {
        formFields[input.name] = input.value;
      }
    };
  }
  function createElementWithStyle(tagName, item) {
    var element = document.createElement(tagName);
    var rect = PDFJS.Util.normalizeRect(
            viewport.convertToViewportRectangle(item.rect));
    element.style.left = Math.floor(rect[0]) + 'px';
    element.style.top = Math.floor(rect[1]) + 'px';
    element.style.width = Math.ceil(rect[2] - rect[0]) + 'px';
    element.style.height = Math.ceil(rect[3] - rect[1]) + 'px';
    return element;
  }
  function assignFontStyle(element, item) {
    var fontStyles = '';
    if ('fontSize' in item) {
      fontStyles += 'font-size: ' + Math.round(item.fontSize *
      viewport.fontScale) + 'px;';
    }
    switch (item.textAlignment) {
      case 0:
        fontStyles += 'text-align: left;';
        break;
      case 1:
        fontStyles += 'text-align: center;';
        break;
      case 2:
        fontStyles += 'text-align: right;';
        break;
    }
    element.setAttribute('style', element.getAttribute('style') + fontStyles);
  }

  content.getAnnotations().then(function(items) {
    for (var i = 0; i < items.length; i++) {
      var item = items[i];
      switch (item.subtype) {
        case 'Widget':
          if (item.fieldType != 'Tx' && item.fieldType != 'Btn' &&
                  item.fieldType != 'Ch') {
            break;
          }
          var inputDiv = createElementWithStyle('div', item);
          inputDiv.className = 'inputHint';
          div.appendChild(inputDiv);
          var input;
          if (item.fieldType == 'Tx') {
            input = createElementWithStyle('input', item);
          }
          if (item.fieldType == 'Btn') {
            input = createElementWithStyle('input', item);
            if (item.flags & 32768) {
              input.type = 'radio';
              // radio button is not supported
            } else if (item.flags & 65536) {
              input.type = 'button';
              // pushbutton is not supported
            } else {
              input.type = 'checkbox';
            }
          }
          if (item.fieldType == 'Ch') {
            input = createElementWithStyle('select', item);
            // select box is not supported
          }
          input.className = 'inputControl';
          input.name = item.fullName;
          input.title = item.alternativeText;
          assignFontStyle(input, item);
          bindInputItem(input, item);
          div.appendChild(input);
          break;
      }
    }
  });
}

function renderPage(div, pdf, pageNumber, callback) {
  pdf.getPage(pageNumber).then(function(page) {
    var scale = $('#viewer').attr('data-scale');
    var viewport = page.getViewport(scale);

    var pageDisplayWidth = viewport.width;
    var pageDisplayHeight = viewport.height;

    var pageDivHolder = document.createElement('div');
    pageDivHolder.className = 'pdfpage';
    pageDivHolder.style.width = pageDisplayWidth + 'px';
    pageDivHolder.style.height = pageDisplayHeight + 'px';
    div.appendChild(pageDivHolder);

    // Prepare canvas using PDF page dimensions
    var canvas = document.createElement('canvas');
    var context = canvas.getContext('2d');
    canvas.width = pageDisplayWidth;
    canvas.height = pageDisplayHeight;
    pageDivHolder.appendChild(canvas);

    // Render PDF page into canvas context
    var renderContext = {
      canvasContext: context,
      viewport: viewport
    };
    page.render(renderContext).promise.then(callback);

    // Prepare and populate form elements layer
    var formDiv = document.createElement('div');
    pageDivHolder.appendChild(formDiv);

    setupForm(formDiv, page, viewport);
  });
}

// Fetch the PDF document from the URL using promices
PDFJS.getDocument(pdfWithFormsPath).then(function getPdfForm(pdf) {
  // Rendering all pages starting from first
  var viewer = document.getElementById('viewer');
  var pageNumber = 1;
  renderPage(viewer, pdf, pageNumber++, function pageRenderingComplete() {
    var percent = (pageNumber - 1) / pdf.numPages * 100;
    changeProgress(percent);
    if (pageNumber > pdf.numPages) {
      showEbook();
      return; // All pages rendered
    }
    // Continue rendering of the next page
    renderPage(viewer, pdf, pageNumber++, pageRenderingComplete);
  });
});


function changeProgress(percent) {
  jQuery('.progress-bar').css({width: percent + 1 + '%'});
}

function showEbook() {
  var viewer = $('#viewer');
  viewer.fadeIn('slow');
  $('.progress').remove();

  var height = $('canvas').height();
  var width = ($('canvas').width() * 2);

  $('.hard img').width(width/2);
  $('.hard img').height(height);

  function loadApp() {
	viewer.turn({
        width:width,
        height:height,
        duration: 1000,
        gradients: true,
        autoCenter: true,
        elevation: 50,
        pages: 4,
        when: {
            turning: function(event, page, view) {
                var book = $(this),
                currentPage = book.turn('page'),
                pages = book.turn('pages');
                // Update the current URI
                Hash.go('page/' + page).update();
                // Show and hide navigation buttons
                disableControls(page);
            },

            turned: function(event, page, view) {
                disableControls(page);
                $(this).turn('center');
                if (page==1) {
                    $(this).turn('peel', 'br');
                }
            },

            missing: function (event, pages) {
                // Add pages that aren't in the magazine
                for (var i = 0; i < pages.length; i++)
                    addPage(pages[i], $(this));
            }
        }
	});

    $(document).keydown(function(e){
		var previous = 37, next = 39, esc = 27;

		switch (e.keyCode) {
			case previous:
				// left arrow
				$('.magazine').turn('previous');
				e.preventDefault();
			break;
			case next:
				//right arrow
				$('.magazine').turn('next');
				e.preventDefault();
			break;
			case esc:
				$('.magazine-viewport').zoom('zoomOut');
				e.preventDefault();
			break;
		}
	});

    Hash.on('^page\/([0-9]*)$', {
		yep: function(path, parts) {
			var page = parts[1];

			if (page!==undefined) {
				if ($('.magazine').turn('is'))
					$('.magazine').turn('page', page);
			}
		},
		nop: function(path) {
			if ($('.magazine').turn('is'))
				$('.magazine').turn('page', 1);
		}
	});

    $(window).resize(function() {
		resizeViewport();
	}).bind('orientationchange', function() {
		resizeViewport();
	});

    if ($.isTouch) {
		$('.magazine').bind('touchstart', regionClick);
	} else {
		$('.magazine').click(regionClick);
	}

    $('.next-button').bind($.mouseEvents.over, function() {
		$(this).addClass('next-button-hover');
	}).bind($.mouseEvents.out, function() {
		$(this).removeClass('next-button-hover');
	}).bind($.mouseEvents.down, function() {
		$(this).addClass('next-button-down');
	}).bind($.mouseEvents.up, function() {
		$(this).removeClass('next-button-down');
	}).click(function() {
		$('.magazine').turn('next');
	});

    $('.previous-button').bind($.mouseEvents.over, function() {
		$(this).addClass('previous-button-hover');
	}).bind($.mouseEvents.out, function() {
		$(this).removeClass('previous-button-hover');
	}).bind($.mouseEvents.down, function() {
		$(this).addClass('previous-button-down');
	}).bind($.mouseEvents.up, function() {
		$(this).removeClass('previous-button-down');
	}).click(function() {
		$('.magazine').turn('previous');
	});

    resizeViewport();
    $('.magazine').addClass('animated');
  }
  loadApp();
}