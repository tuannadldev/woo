var asl_engine = {};

(function($, app_engine) {
  'use strict';


  /* API method to get paging information */
  $.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings ){return {"iStart":         oSettings._iDisplayStart,"iEnd":           oSettings.fnDisplayEnd(),"iLength":        oSettings._iDisplayLength,"iTotal":         oSettings.fnRecordsTotal(),"iFilteredTotal": oSettings.fnRecordsDisplay(),"iPage":          oSettings._iDisplayLength === -1 ?0 : Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),"iTotalPages":    oSettings._iDisplayLength === -1 ?0 : Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )};};

  /* Bootstrap style pagination control */
  $.extend($.fn.dataTableExt.oPagination,{bootstrap:{fnInit:function(i,a,e){var t=i.oLanguage.oPaginate,l=function(a){a.preventDefault(),i.oApi._fnPageChange(i,a.data.action)&&e(i)};$(a).addClass("pagination").append('<ul class="pagination mt-3"><li class="page-item prev disabled"><a class="page-link" href="#">&larr; '+t.sPrevious+'</a></li><li class="page-item next disabled"><a class="page-link" href="#">'+t.sNext+" &rarr; </a></li></ul>");var s=$("a",a);$(s[0]).bind("click.DT",{action:"previous"},l),$(s[1]).bind("click.DT",{action:"next"},l)},fnUpdate:function(i,e){var a,t,l,s,n,o=i.oInstance.fnPagingInfo(),g=i.aanFeatures.p,r=Math.floor(2.5);n=o.iTotalPages<5?(s=1,o.iTotalPages):o.iPage<=r?(s=1,5):o.iPage>=o.iTotalPages-r?(s=o.iTotalPages-5+1,o.iTotalPages):(s=o.iPage-r+1)+5-1;var d=g.length;for(a=0;a<d;a++){for($("li:gt(0)",g[a]).filter(":not(:last)").remove(),t=s;t<=n;t++)l=t==o.iPage+1?"active":"",$('<li class="page-item '+l+'"><a class="page-link" href="#">'+t+"</a></li>").insertBefore($("li:last",g[a])[0]).bind("click",function(a){a.preventDefault(),i._iDisplayStart=(parseInt($("a",this).text(),10)-1)*o.iLength,e(i)});0===o.iPage?$("li:first",g[a]).addClass("disabled"):$("li:first",g[a]).removeClass("disabled"),o.iPage===o.iTotalPages-1||0===o.iTotalPages?$("li:last",g[a]).addClass("disabled"):$("li:last",g[a]).removeClass("disabled")}}}});
  

  function codeAddress(_address, _callback) {

    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({ 'address': _address }, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        _callback(results[0].geometry);
      } else {
        atoastr.error(ASL_REMOTE.LANG.geocode_fail + status);
      }
    });
  };


  function isEmpty(obj) {

    if (obj == null) return true;
    if (typeof(obj) == 'string' && obj == '') return true;
    return Object.keys(obj).length === 0;
  };

  // Asynchronous load
  var map,
    map_object = {
      is_loaded: true,
      marker: null,
      changed: false,
      store_location: null,
      map_marker: null,
      intialize: function(_callback) {

        var API_KEY = '';
        if (asl_configs && asl_configs.api_key) {
          API_KEY = '&key=' + asl_configs.api_key;
        }

        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = '//maps.googleapis.com/maps/api/js?libraries=places,drawing&' +
          'callback=asl_map_intialized' + API_KEY;
        //+'callback=asl_map_intialized';
        document.body.appendChild(script);
        this.cb = _callback;
      },
      render_a_map: function(_lat, _lng) {

        var hdlr = this,
          map_div = document.getElementById('map_canvas'),
          _draggable = true;

        hdlr.store_location = (_lat && _lng) ? [parseFloat(_lat), parseFloat(_lng)] : [-37.815, 144.965];

        var latlng = new google.maps.LatLng(hdlr.store_location[0], hdlr.store_location[1]);

        if (!map_div) return false;

        var mapOptions = {
          zoom: 14,
          minZoom: 8,
          center: latlng,
          //maxZoom: 10,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          styles: [{ "stylers": [{ "saturation": -100 }, { "gamma": 1 }] }, { "elementType": "labels.text.stroke", "stylers": [{ "visibility": "off" }] }, { "featureType": "poi.business", "elementType": "labels.text", "stylers": [{ "visibility": "off" }] }, { "featureType": "poi.business", "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] }, { "featureType": "poi.place_of_worship", "elementType": "labels.text", "stylers": [{ "visibility": "off" }] }, { "featureType": "poi.place_of_worship", "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] }, { "featureType": "road", "elementType": "geometry", "stylers": [{ "visibility": "simplified" }] }, { "featureType": "water", "stylers": [{ "visibility": "on" }, { "saturation": 50 }, { "gamma": 0 }, { "hue": "#50a5d1" }] }, { "featureType": "administrative.neighborhood", "elementType": "labels.text.fill", "stylers": [{ "color": "#333333" }] }, { "featureType": "road.local", "elementType": "labels.text", "stylers": [{ "weight": 0.5 }, { "color": "#333333" }] }, { "featureType": "transit.station", "elementType": "labels.icon", "stylers": [{ "gamma": 1 }, { "saturation": 50 }] }]
        };

        hdlr.map_instance = map = new google.maps.Map(map_div, mapOptions);

        // && navigator.geolocation && _draggable
        if ((!hdlr.store_location || isEmpty(hdlr.store_location[0]))) {

          /*navigator.geolocation.getCurrentPosition(function(position){
          	
          	hdlr.changed = true;
          	hdlr.store_location = [position.coords.latitude,position.coords.longitude];
          	var loc = new google.maps.LatLng(position.coords.latitude,  position.coords.longitude);
          	hdlr.add_marker(loc);
          	map.panTo(loc);
          });*/

          hdlr.add_marker(latlng);
        } else if (hdlr.store_location) {
          if (isNaN(hdlr.store_location[0]) || isNaN(hdlr.store_location[1])) return;
          //var loc = new google.maps.LatLng(hdlr.store_location[0], hdlr.store_location[1]);
          hdlr.add_marker(latlng);
          map.panTo(latlng);
        }
      },
      add_marker: function(_loc) {

        var hdlr = this;

        hdlr.map_marker = new google.maps.Marker({
          draggable: true,
          position: _loc,
          map: map
        });

        var marker_icon = new google.maps.MarkerImage(ASL_Instance.url + 'admin/images/pin1.png');
        marker_icon.size = new google.maps.Size(24, 39);
        marker_icon.anchor = new google.maps.Point(24, 39);
        hdlr.map_marker.setIcon(marker_icon);
        hdlr.map_instance.panTo(_loc);

        google.maps.event.addListener(
          hdlr.map_marker,
          'dragend',
          function() {

            hdlr.store_location = [hdlr.map_marker.position.lat(), hdlr.map_marker.position.lng()];
            hdlr.changed = true;
            var loc = new google.maps.LatLng(hdlr.map_marker.position.lat(), hdlr.map_marker.position.lng());
            //map.setPosition(loc);
            map.panTo(loc);

            app_engine.pages.store_changed(hdlr.store_location);
          });

      }
    };

  //add the uploader
  app_engine.uploader = function($form, _URL, _done /*,_submit_callback*/ ) {


    function formatFileSize(bytes) {
      if (typeof bytes !== 'number') {
        return ''
      }
      if (bytes >= 1000000000) {
        return (bytes / 1000000000).toFixed(2) + ' GB'
      }
      if (bytes >= 1000000) {
        return (bytes / 1000000).toFixed(2) + ' MB'
      }
      return (bytes / 1000).toFixed(2) + ' KB'
    };

    var ul = $form.find('ul');
    $form[0].reset();


    $form.fileupload({
        url: _URL,
        dataType: 'json',
        //multipart: false,
        done: function(e, _data) {

          ul.empty();
          _done(e, _data);

          $form.find('.progress-bar').css('width', '0%');
          $form.find('.progress').hide();

          //reset form if success
          if (_data.result.success) {}
        },
        add: function(e, _data) {

          ul.empty();

          //Check file Extension
          var exten = _data.files[0].name.split('.'),
            exten = exten[exten.length - 1];
          if (['jpg', 'png', 'jpeg', 'gif', 'JPG', 'svg', 'zip', 'xlsx', 'xls', 'csv'].indexOf(exten) == -1) {

            atoastr.error((ASL_REMOTE.LANG.invalid_file_error));
            return false;
          }


          var tpl = $('<li class="working"><p class="col-12 text-muted"><span class="float-left"></span></p></li>');
          tpl.find('p').text(_data.files[0].name.substr(0, 50)).append('<i class="float-right">' + formatFileSize(_data.files[0].size) + '</i>');
          _data.context = tpl.appendTo(ul);

          var jqXHR = null;
          $form.find('.btn-start').unbind().bind('click', function() {

            
            /*if(_submit_callback){
            	if(!_submit_callback())return false;
            }*/

            jqXHR = _data.submit();

            $form.find('.progress').show()
          });


          $form.find('.custom-file-label').html(_data.files[0].name);
        },
        progress: function(e, _data) {
          var progress = parseInt(_data.loaded / _data.total * 100, 10);
          $form.find('.progress-bar').css('width', progress + '%');
          $form.find('.sr-only').html(progress + '%');

          if (progress == 100) {
            _data.context.removeClass('working');
          }
        },
        fail: function(e, _data) {
          _data.context.addClass('error');
          $form.find('.upload-status-box').html(ASL_REMOTE.LANG.upload_fail).addClass('bg-warning alert')
        }
        /*
        formData: function(_form) {

        	var formData = [{
        		name: '_data[action]',
        		value: 'asl_add_store'
        	}]

        	//	console.log(formData);
        	return formData;
        }*/
      })
      .bind('fileuploadsubmit', function(e, _data) {
        _data.formData = $form.serializeObject();
      })
      .prop('disabled', !$.support.fileInput)
      .parent().addClass($.support.fileInput ? undefined : 'disabled');
  };

  //http://harvesthq.github.io/chosen/options.html
  app_engine['pages'] = {
    _validate_page: function() {

      if (ASL_REMOTE.Com) return;

      aswal({
        title: ASL_REMOTE.LANG.pur_title,
        html: ASL_REMOTE.LANG.pur_text,
        input: 'text',
        type: "question",
        showCancelButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "VALIDATE",
        preConfirm: function(_value) {

          return new Promise(function(resolve, reject) {

            if ($.trim(_value) == '') {

              aswal.showValidationError('Purchase Code is Missing!');
              return false;
            }

            aswal.showLoading();

            ServerCall(ASL_REMOTE.URL + "?action=asl_validate_me", { value: _value }, function(_response) {

              aswal.hideLoading();

              if (!_response.success) {

                aswal.showValidationError(_response.message);
                reject();
                return false;
              } else {

                aswal({
                  type: (_response.success) ? 'success' : 'error',
                  title: (_response.success) ? 'Validate Successfully!' : 'Validation Failed!',
                  html: (_response.message) ? _response.message : ('Validation Failed, Please Contact Support')
                });

                reject();
                return true;
              }

            }, 'json');

          })
        }
        /*inputValidator: function(value) {
					    return !value && 'You need to write something!'
					}*/
      })
    },
    store_changed: function(_position) {

      $('#asl_txt_lat').val(_position[0]);
      $('#asl_txt_lng').val(_position[1]);
    },
    manage_categories: function() {

      var table = null;

      //prompt the category box
      $('#btn-asl-new-c').bind('click', function() {
        $('#asl-add-modal').smodal('show');
      });


      var asInitVals = {};
      table = $('#tbl_categories').dataTable({
        "sPaginationType": "bootstrap",
        "bProcessing": true,
        "bFilter": false,
        "bServerSide": true,
        //"scrollX": true,
        /*"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 1 ] }
        ],*/
        "bAutoWidth": true,
        "columnDefs": [
          { 'bSortable': false, "width": "75px", "targets": 0 },
          { "width": "75px", "targets": 1 },
          { "width": "200px", "targets": 2 },
          { "width": "100px", "targets": 3 },
          { "width": "100px", "targets": 4 },
          { "width": "150px", "targets": 5 },
          { "width": "150px", "targets": 6 },
          { 'bSortable': false, 'aTargets': [0, 6] }
        ],
        "iDisplayLength": 10,
        "sAjaxSource": ASL_REMOTE.URL + "?action=asl_get_categories",
        "columns": [
          { "data": "check" },
          { "data": "id" },
          { "data": "category_name" },
          { "data": "is_active" },
          { "data": "icon" },
          { "data": "created_on" },
          { "data": "action" }
        ],
        'fnServerData': function(sSource, aoData, fnCallback) {

          $.get(sSource, aoData, function(json) {

            fnCallback(json);

          }, 'json');

        },
        "fnServerParams": function(aoData) {

          $("thead input").each(function(i) {

            if (this.value != "") {
              aoData.push({
                "name": 'filter[' + $(this).attr('data-id') + ']',
                "value": this.value
              });
            }
          });
        },
        "order": [
          [1, 'desc']
        ]
      });


      //Select all button
      $('.table .select-all').bind('click', function(e) {

        $('.asl-p-cont .table input').attr('checked', 'checked');

      });

      //Delete Selected Categories:: bulk
      $('#btn-asl-delete-all').bind('click', function(e) {

        var $tmp_categories = $('.asl-p-cont .table input:checked');

        if ($tmp_categories.length == 0) {
          atoastr.error('No Category selected');
          return;
        }

        var item_ids = [];
        $('.asl-p-cont .table input:checked').each(function(i) {

          item_ids.push($(this).attr('data-id'));
        });


        aswal({
          title: ASL_REMOTE.LANG.delete_categories,
          text: ASL_REMOTE.LANG.warn_question + ' ' + ASL_REMOTE.LANG.delete_categories + '?',
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: ASL_REMOTE.LANG.delete_it
        }).then(function() {

          ServerCall(ASL_REMOTE.URL + "?action=asl_delete_category", { item_ids: item_ids, multiple: true }, function(_response) {

            if (_response.success) {
              atoastr.success(_response.msg);
              table.fnDraw();
              return;
            } else {
              atoastr.error((_response.error || ASL_REMOTE.LANG.error_try_again));
              return;
            }

          }, 'json');
        });
      });


      //TO ADD NEW Categories
      var url_to_upload = ASL_REMOTE.URL,
        $form = $('#frm-addcategory');

      app_engine.uploader($form, url_to_upload + '?action=asl_add_categories', function(e, data) {

        var data = data.result;

        if (!data.success) {

          atoastr.error(data.msg);
        } else {

          atoastr.success(data.msg);
          //reset form
          $('#asl-add-modal').smodal('hide');
          $('#frm-addcategory').find('input:text, input:file').val('');
          $('#progress_bar').hide();
          //show table value
          table.fnDraw();
        }
      });

      //Validate
      $('#btn-asl-add-categories').bind('click', function(e) {

        if ($('#frm-addcategory ul li').length == 0) {

          atoastr.error('Please Upload Category Icon');

          e.preventDefault();
          return;
        }
      });

      //show edit category model
      $('#tbl_categories tbody').on('click', '.edit_category', function(e) {

        $('#updatecategory_image').show();
        $('#updatecategory_editimage').hide();
        $('#asl-update-modal').smodal('show');
        $('#update_category_id_input').val($(this).attr("data-id"));

        ServerCall(ASL_REMOTE.URL + "?action=asl_get_category_byid", { category_id: $(this).attr("data-id") }, function(_response) {

          if (_response.success) {

            $("#update_category_name").val(_response.list[0]['category_name']);
            $("#update_category_icon").attr("src", ASL_Instance.url + "public/svg/" + _response.list[0]['icon']);
          } else {

            atoastr.error(_response.error);
            return;
          }
        }, 'json');
      });

      //show edit category upload image
      $('#change_image').click(function() {

        $("#update_category_icon").attr("data-id", "")
        $('#updatecategory_image').hide();
        $('#updatecategory_editimage').show();
      });

      //	Update category without icon
      $('#btn-asl-update-categories').click(function() {

        if ($("#update_category_icon").attr("data-id") == "same") {

          ServerCall(ASL_REMOTE.URL + "?action=asl_update_category", { data: { category_id: $("#update_category_id").text(), action: "same", category_name: $("#update_category_name").val() } },
            function(_response) {

              if (_response.success) {

                atoastr.success(_response.msg);

                table.fnDraw();

                return;
              } else if (_response.error) {
                atoastr.error(_response.msg);
                return;
              }
            }, 'json');

        }

      });

      //	Update category with icon

      var url_to_upload = ASL_REMOTE.URL,
        $form = $('#frm-updatecategory');

      $form.append('<input type="hidden" name="data[action]" value="notsame" /> ');

      app_engine.uploader($form, url_to_upload + '?action=asl_update_category', function(e, data) {

        var data = data.result;

        if (data.success) {

          atoastr.success(data.msg);
          $('#asl-update-modal').smodal('hide');
          $('#frm-updatecategory').find('input:text, input:file').val('');
          $('#progress_bar_').hide();
          table.fnDraw();
        } else
          atoastr.error(data.msg);
      });

      //show delete category model
      $('#tbl_categories tbody').on('click', '.delete_category', function(e) {

        var _category_id = $(this).attr("data-id");

        aswal({
          title: ASL_REMOTE.LANG.delete_category,
          text: ASL_REMOTE.LANG.warn_question + ' ' + ASL_REMOTE.LANG.delete_category + ' ' + _category_id + " ?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: ASL_REMOTE.LANG.delete_it,
        }).then(
          function() {

            ServerCall(ASL_REMOTE.URL + "?action=asl_delete_category", { category_id: _category_id }, function(_response) {

              if (_response.success) {
                atoastr.success(_response.msg);
                table.fnDraw();
                return;
              } else {
                atoastr.error((_response.error || ASL_REMOTE.LANG.error_try_again));
                return;
              }

            }, 'json');

          }
        );
      });



      $("thead input").keyup(function(e) {

        if (e.keyCode == 13) {
          table.fnDraw();
        }
      });
    },
    /**
     * [manage_markers Manage Markers]
     * @return {[type]} [description]
     */
    manage_markers: function() {

      var table = null;

      //prompt the marker box
      $('#btn-asl-new-c').bind('click', function() {
        $('#asl-add-modal').smodal('show');
      });


      var asInitVals = {};
      table = $('#tbl_markers').dataTable({
        "sPaginationType": "bootstrap",
        "bProcessing": true,
        "bFilter": false,
        "bServerSide": true,
        //"scrollX": true,
        /*"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 1 ] }
        ],*/
        "bAutoWidth": true,
        "columnDefs": [
          { 'bSortable': false, "width": "75px", "targets": 0 },
          { "width": "75px", "targets": 1 },
          { "width": "200px", "targets": 2 },
          { "width": "100px", "targets": 3 },
          { "width": "100px", "targets": 4 },
          { "width": "150px", "targets": 5 },
          { 'bSortable': false, 'aTargets': [5] }
        ],
        "iDisplayLength": 10,
        "sAjaxSource": ASL_REMOTE.URL + "?action=asl_get_markers",
        "columns": [
          { "data": "check" },
          { "data": "id" },
          { "data": "marker_name" },
          { "data": "is_active" },
          { "data": "icon" },
          { "data": "action" }
        ],
        "fnServerParams": function(aoData) {

          $("#tbl_markers_wrapper thead input").each(function(i) {

            if (this.value != "") {
              aoData.push({
                "name": 'filter[' + $(this).attr('data-id') + ']',
                "value": this.value
              });
            }
          });
        },
        "order": [
          [1, 'desc']
        ]
      });


      //TO ADD New Marker
      var url_to_upload = ASL_REMOTE.URL,
        $form           = $('#frm-addmarker');

      app_engine.uploader($form, url_to_upload + '?action=asl_add_markers', function(e, data) {

        var data = data.result;

        if (!data.success) {

          atoastr.error(data.msg);
        } else {

          atoastr.success(data.msg);
          //reset form
          $('#asl-add-modal').smodal('hide');
          $('#frm-addmarker').find('input:text, input:file').val('');
          $('#progress_bar').hide();
          //show table value
          table.fnDraw();
        }
      });


      //  Show edit marker model
      $('#tbl_markers tbody').on('click', '.edit_marker', function(e) {

        $('#message_update').empty().removeAttr('class');
        $('#updatemarker_image').show();
        $('#updatemarker_editimage').hide();
        $('#asl-update-modal').smodal('show');
        $('#update_marker_id_input').val($(this).attr("data-id"));

        ServerCall(ASL_REMOTE.URL + "?action=asl_get_marker_byid", { marker_id: $(this).attr("data-id") }, function(_response) {

          if (_response.success) {

            $("#update_marker_name").val(_response.list[0]['marker_name']);
            $("#update_marker_icon").attr("src", ASL_Instance.url + "public/icon/" + _response.list[0]['icon']);
          } else {

            atoastr.error(_response.error);
            return;
          }
        }, 'json');
      });

      //show edit marker upload image
      $('#change_image').click(function() {

        $("#update_marker_icon").attr("data-id", "")
        $('#updatemarker_image').hide();
        $('#updatemarker_editimage').show();
      });

      //update marker without icon
      $('#btn-asl-update-markers').click(function() {

        if ($("#update_marker_icon").attr("data-id") == "same") {

          ServerCall(ASL_REMOTE.URL + "?action=asl_update_marker", { data: { marker_id: $("#update_marker_id_input").val(), action: "same", marker_name: $("#update_marker_name").val() } },
            function(_response) {

              if (_response.success) {

                atoastr.success(_response.msg);

                table.fnDraw();

                return;
              } else {
                atoastr.error(_response.msg);
                return;
              }

            }, 'json');

        }

      });

      //  Update marker with icon
      var url_to_upload = ASL_REMOTE.URL,
          $form         = $('#frm-updatemarker');

      $form.append('<input type="hidden" name="data[action]" value="notsame" /> ');

      app_engine.uploader($form, url_to_upload + '?action=asl_update_marker', function(e, data) {

        var data = data.result;

        if (data.success) {

          atoastr.success(data.msg);
          $('#asl-update-modal').smodal('hide');
          $('#frm-updatemarker').find('input:text, input:file').val('');
          $('#progress_bar_').hide();
          table.fnDraw();
        }
        else
          atoastr.error(data.msg);
      });

      //  Show delete marker model
      $('#tbl_markers tbody').on('click', '.delete_marker', function(e) {

        var _marker_id = $(this).attr("data-id");

        aswal({
          title: ASL_REMOTE.LANG.delete_marker,
          text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.delete_marker + " " + _marker_id + "?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: ASL_REMOTE.LANG.delete_it,
        }).then(
          function() {

            ServerCall(ASL_REMOTE.URL + "?action=asl_delete_marker", { marker_id: _marker_id }, function(_response) {

              if (_response.success) {
                atoastr.success(_response.msg);
                table.fnDraw();
                return;
              } else {
                atoastr.error((_response.error || ASL_REMOTE.LANG.error_try_again));
                return;
              }

            }, 'json');

          }
        );
      });

      //////////////Delete Selected Categories////////////////

      //  Select all button
      $('.table .select-all').bind('click', function(e) {

        $('.asl-p-cont .table input').attr('checked', 'checked');
      });

      //Bulk
      $('#btn-asl-delete-all').bind('click', function(e) {

        var $tmp_markers = $('.asl-p-cont .table input:checked');

        if ($tmp_markers.length == 0) {
          atoastr.error('No Marker selected');
          return;
        }

        var item_ids = [];
        $('.asl-p-cont .table input:checked').each(function(i) {

          item_ids.push($(this).attr('data-id'));
        });


        aswal({
            title: ASL_REMOTE.LANG.delete_markers,
            text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.delete_markers + "?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: ASL_REMOTE.LANG.delete_it
          })
          .then(function() {

            ServerCall(ASL_REMOTE.URL + "?action=asl_delete_marker", { item_ids: item_ids, multiple: true }, function(_response) {

              if (_response.success) {
                atoastr.success(_response.msg);
                table.fnDraw();
                return;
              } else {
                atoastr.error((_response.error || ASL_REMOTE.LANG.error_try_again));
                return;
              }

            }, 'json');
          });

      });

      $("thead input").keyup(function(e) {

        if (e.keyCode == 13) {
          table.fnDraw();
        }
      });

    },
    manage_logos: function() {

      var table = null;

      //prompt the logo box
      $('#btn-asl-new-c').bind('click', function() {
        $('#asl-add-modal').smodal('show');
      });


      var asInitVals = {};
      table = $('#tbl_logos').dataTable({
        "sPaginationType": "bootstrap",
        "bProcessing": true,
        "bFilter": false,
        "bServerSide": true,
        //"scrollX": true,
        /*"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 1 ] }
        ],*/
        "bAutoWidth": true,
        "columnDefs": [
          { 'bSortable': false, "width": "75px", "targets": 0 },
          { "width": "75px", "targets": 1 },
          { "width": "200px", "targets": 2 },
          { "width": "100px", "targets": 3 },
          { "width": "150px", "targets": 4 },
          { 'bSortable': false, 'aTargets': [4] }
        ],
        "iDisplayLength": 10,
        "sAjaxSource": ASL_REMOTE.URL + "?action=asl_get_logos",
        "columns": [
          { "data": "check" },
          { "data": "id" },
          { "data": "name" },
          { "data": "path" },
          { "data": "action" }
        ],
        "fnServerParams": function(aoData) {

          $("#tbl_logos_wrapper thead input").each(function(i) {

            if (this.value != "") {
              aoData.push({
                "name": 'filter[' + $(this).attr('data-id') + ']',
                "value": this.value
              });
            }
          });
        },
        "order": [
          [1, 'desc']
        ]
      });


      //TO ADD NEW Categories
      var url_to_upload = ASL_REMOTE.URL,
        $form = $('#frm-addlogo');

      app_engine.uploader($form, url_to_upload + '?action=asl_upload_logo', function(e, data) {

        var data = data.result;

        if (!data.success) {

          atoastr.error(data.msg);
        }
        else {

          atoastr.success(data.msg);
          
          //  Reset form
          $('#asl-add-modal').smodal('hide');
          $('#frm-addlogo').find('input:text, input:file').val('');
          $('#progress_bar').hide();
          //show table value
          table.fnDraw();
        }
      });


      //show edit logo model
      $('#tbl_logos tbody').on('click', '.edit_logo', function(e) {

        $('#updatelogo_image').show();
        $('#updatelogo_editimage').hide();
        $('#asl-update-modal').smodal('show');
        $('#update_logo_id_input').val($(this).attr("data-id"));

        ServerCall(ASL_REMOTE.URL + "?action=asl_get_logo_byid", { logo_id: $(this).attr("data-id") }, function(_response) {

          if (_response.success) {

            $("#update_logo_name").val(_response.list[0]['name']);
            $("#update_logo_icon").attr("src", ASL_Instance.url + "public/Logo/" + _response.list[0]['path']);
          } else {

            atoastr.error(_response.error);
            return;
          }
        }, 'json');


      });

      //show edit logo upload image
      $('#change_image').click(function() {

        $("#update_logo_icon").attr("data-id", "")
        $('#updatelogo_image').hide();
        $('#updatelogo_editimage').show();
      });

      //update logo without icon
      $('#btn-asl-update-logos').click(function() {

        if ($("#update_logo_icon").attr("data-id") == "same") {

          ServerCall(ASL_REMOTE.URL + "?action=asl_update_logo", { data: { logo_id: $("#update_logo_id_input").val(), action: "same", logo_name: $("#update_logo_name").val() } },
            function(_response) {

              if (_response.success) {

                atoastr.success(_response.msg);

                table.fnDraw();

                return;
              }
              else {
                atoastr.error(_response.msg);
                return;
              }
            }, 'json');

        }

      });

      //  Update logo with icon
      var url_to_upload = ASL_REMOTE.URL,
          $form         = $('#frm-updatelogo');

      $form.append('<input type="hidden" name="data[action]" value="notsame" /> ');

      app_engine.uploader($form, url_to_upload + '?action=asl_update_logo', function(e, data) {

        var data = data.result;

        
        if (data.success) {

          atoastr.success(data.msg);
          $('#asl-update-modal').smodal('hide');
          $('#frm-updatelogo').find('input:text, input:file').val('');
          $('#progress_bar_').hide();
          table.fnDraw();
        }
        else
          atoastr.error(data.msg);
      });

      //show delete logo model
      $('#tbl_logos tbody').on('click', '.delete_logo', function(e) {

        var _logo_id = $(this).attr("data-id");

        aswal({
          title: ASL_REMOTE.LANG.delete_logo,
          text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.delete_logo + " " + _logo_id + "?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: ASL_REMOTE.LANG.delete_it,
        }).then(
          function() {

            ServerCall(ASL_REMOTE.URL + "?action=asl_delete_logo", { logo_id: _logo_id }, function(_response) {

              if (_response.success) {
                atoastr.success(_response.msg);
                table.fnDraw();
                return;
              } else {
                atoastr.success((_response.error || ASL_REMOTE.LANG.error_try_again));
                return;
              }

            }, 'json');

          }
        );
      });

      //////////////Delete Selected Categories////////////////

      //Select all button
      $('.table .select-all').bind('click', function(e) {

        $('.asl-p-cont .table input').attr('checked', 'checked');
      });

      //Bulk
      $('#btn-asl-delete-all').bind('click', function(e) {

        var $tmp_logos = $('.asl-p-cont .table input:checked');

        if ($tmp_logos.length == 0) {
          atoastr.error('No Logo selected');
          return;
        }

        var item_ids = [];
        $('.asl-p-cont .table input:checked').each(function(i) {

          item_ids.push($(this).attr('data-id'));
        });

        aswal({
          title: ASL_REMOTE.LANG.delete_logos,
          text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.delete_logos + "?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: ASL_REMOTE.LANG.delete_it,
        }).then(function() {

          ServerCall(ASL_REMOTE.URL + "?action=asl_delete_logo", { item_ids: item_ids, multiple: true }, function(_response) {

            if (_response.success) {
              atoastr.success(_response.msg);
              table.fnDraw();
              return;
            } else {
              atoastr.success((_response.error || ASL_REMOTE.LANG.error_try_again));
              return;
            }

          }, 'json');
        });

      });


      $("thead input").keyup(function(e) {

        if (e.keyCode == 13) {
          table.fnDraw();
        }
      });
    },
    dashboard: function() {

      var current_date = 0,
        date_ = new Date();

      var day_arr = [];
      var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        month = months[date_.getMonth()],
        data_arr = [];


      $('.asl-p-cont .nav-tabs a').click(function(e) {
        e.preventDefault()
        $(this).tab('show');
      })
      //Reset
      $('#asl-search-month')[0].selectedIndex = 0;


      for (var a = 1; a <= date_.getDate(); a++) {

        day_arr.push(a + ' ' + month);
        data_arr.push(0);
      }

      var barChartData = {
        labels: day_arr,
        datasets: [{
          label: '',
          backgroundColor: "#57C8F2",
          data: data_arr
        }]

      };

      asl_initialize_chart();

      ///Button to backup Assets
      $('#btn-assets-backup').bind('click', function(e) {

        var $this = $(this);

        //$this.bootButton('loading');
        $('#lnk-assets-download').addClass('hide');

        ServerCall(ASL_REMOTE.URL + '?action=asl_backup_assets', {}, function(_response) {


          if (_response.success) {

            $('#lnk-assets-download').attr('href', _response.zip).removeClass('hide');
            atoastr.success(_response.msg);
          } else if (_response.error) {
            atoastr.error(_response.error);
            return;
          }

        }, 'json');
      });

      //upload Assets Zip
      var url_to_upload = ASL_REMOTE.URL,
        $form_upload = $('#import_assets_file');

      app_engine.uploader($form_upload, url_to_upload + '?action=asl_import_assets', function(_e, _data) {

        var data = _data.result;

        if (data.success) {

          atoastr.success(data.msg);
        } else {

          atoastr.error(data.error);
        }

        $('#import_assets_model').smodal('hide');
        $('#progress_bar_').hide();
      });


      ///////////bar chart
      var ctx = document.getElementById("asl_search_canvas").getContext("2d"),
        charts_option = {
          type: 'bar',
          data: barChartData,
          options: {
            scaleStartValue: 0,
            scaleBeginAtZero: 0,

            /*animation: {
                animateRotate: false,
                animateScale: false
            },*/

            elements: {
              rectangle: {
                borderWidth: 0
              }
            },
            responsive: true,
            legend: {
              position: 'top',
            },
            title: {
              display: true,
              text: '#Searches'
            },
            scales: {
              yAxes: [{
                ticks: {
                  beginAtZero: true
                }
              }]
            }
          }
        };
      var myBar = new Chart(ctx, charts_option);


      function updateChart(m, y) {

        m = parseInt(m);
        y = parseInt(y);


        ServerCall(ASL_REMOTE.URL + "?action=asl_get_stats", { m: m, y: y }, function(_response) {

          var keys = Object.keys(_response.data);

          var temp_keys = [],
            temp_vals = [];


          for (var k in keys) {

            temp_keys.push(keys[k] + ' ' + months[m - 1]);
            temp_vals.push(_response.data[keys[k]]);
          }

          myBar.config.data.labels = temp_keys;
          myBar.config.data.datasets[0].data = temp_vals;
          myBar.update();

        }, 'json');

      };

      var temp = $('#asl-search-month')[0].value.split('-');
      updateChart(temp[0], temp[1]);

      $('#asl-search-month').bind('change', function(e) {

        var temp = this.value.split('-'),
          m = temp[0],
          y = temp[1];

        updateChart(m, y);

      });



    },
    manage_stores: function() {

      var table = null,
        row_duplicate_id = null;


      /*DUPLICATE STORES*/
      var duplicate_store = function(_id) {

        ServerCall(ASL_REMOTE.URL + "?action=asl_duplicate_store", { store_id: _id }, function(_response) {

          if (_response.success) {
            atoastr.success(_response.msg);
            table.fnDraw();
            return;
          } else if (_response.error) {
            atoastr.error(_response.error);
            return;
          } else {
            atoastr.error(ASL_REMOTE.LANG.error_try_again);
          }
        }, 'json');
      };

      //Prompt the DUPLICATE alert
      $('#tbl_stores').on('click', '.row-cpy', function() {

        row_duplicate_id = $(this).data('id');

        aswal({
            title: ASL_REMOTE.LANG.duplicate_stores,
            text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.duplicate_stores + "?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: ASL_REMOTE.LANG.duplicate_it,
          })
          .then(
            function() {

              duplicate_store(row_duplicate_id);
            }
          );

      });


      /*Delete Stores*/
      var _delete_all_stores = function() {

        var $this = $('#asl-delete-stores');
        $this.bootButton('loading');

        ServerCall(ASL_REMOTE.URL + '?action=asl_delete_all_stores', {}, function(_response) {

          $this.bootButton('reset');
          atoastr.success(_response.msg);
          table.fnDraw();
        }, 'json');
      };

      /*Delete All stores*/
      $('#asl-delete-stores').bind('click', function(e) {

        aswal({
          title: ASL_REMOTE.LANG.delete_all_stores,
          text: ASL_REMOTE.LANG.warn_question + ' ' + ASL_REMOTE.LANG.delete_all_stores + "?",
          type: "error",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: ASL_REMOTE.LANG.delete_all
        }).then(
          function() {

            _delete_all_stores();
          }
        );
      });

      //duplicate the alert
      /*
      $('#btn-duplicate-store').bind('click',function(){

      	$('#confirm-duplicate').smodal('hide');	
      	duplicate_store(row_duplicate_id);
      });
      */

      
      var asInitVals = {};
      table = $('#tbl_stores').dataTable({
        "sPaginationType": "bootstrap",
        "bProcessing": true,
        "bFilter": false,
        "bServerSide": true,
        "scrollX": true,
        /*"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 1 ] }
        ],*/
        "bAutoWidth": false,
        "columnDefs": [
          { "width": "2000px", "targets": 0 },
          { "width": "250px", "targets": 1 },
          { "width": "100px", "targets": 2 },
          { "width": "200px", "targets": 3 },
          { "width": "300px", "targets": 4 },
          { "width": "300px", "targets": 5 },
          { "width": "300px", "targets": 6 },
          { "width": "300px", "targets": 7 },
          { "width": "150px", "targets": 8 },
          { "width": "150px", "targets": 9 },
          { "width": "150px", "targets": 10 },
          { "width": "150px", "targets": 11 },
          { "width": "150px", "targets": 12 },
          { "width": "150px", "targets": 13 },
          { "width": "50px", "targets": 14 },
          { "width": "350px", "targets": 15 },
          { "width": "50px", "targets": 16 },
          { "width": "50px", "targets": 17 },
          { "width": "150px", "targets": 18 },
          { 'bSortable': false, 'aTargets': [0, 15, 1] }
        ],
        "iDisplayLength": 10,
        "sAjaxSource": ASL_REMOTE.URL + "?action=asl_get_store_list",
        "columns": [
          { "data": "check" },
          { "data": "action" },
          { "data": "id" },
          { "data": "title" },
          { "data": "description" },
          { "data": "lat" },
          { "data": "lng" },
          { "data": "street" },
          { "data": "state" },
          { "data": "city" },
          { "data": "phone" },
          { "data": "email" },
          { "data": "website" },
          { "data": "postal_code" },
          { "data": "is_disabled" },
          { "data": "categories" },
          { "data": "marker_id" },
          { "data": "logo_id" },
          { "data": "created_on" }
        ],
        "fnServerParams": function(aoData) {

          $("#tbl_stores_wrapper .dataTables_scrollHead thead input").each(function(i) {

            if (this.value != "") {
              aoData.push({
                "name": 'filter[' + $(this).attr('data-id') + ']',
                "value": this.value

              });
            }
          });

        },
        "order": [
          [2, 'desc']
        ]
      });

      //oTable.fnSort( [ [10,'desc'] ] );

      //Select all button
      $('.table .select-all').bind('click', function(e) {

        $('.asl-p-cont .table input').attr('checked', 'checked');
      });

      //Delete Selected Stores:: bulk
      $('#btn-asl-delete-all').bind('click', function(e) {

        var $tmp_stores = $('.asl-p-cont .table input:checked');

        if ($tmp_stores.length == 0) {
          atoastr.error('No Store selected');
          return;
        }

        var item_ids = [];
        $('.asl-p-cont .table input:checked').each(function(i) {

          item_ids.push($(this).attr('data-id'));
        });


        aswal({
            title: ASL_REMOTE.LANG.delete_stores,
            text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.delete_stores + "?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: ASL_REMOTE.LANG.delete_it,
          })
          .then(
            function() {

              ServerCall(ASL_REMOTE.URL + "?action=asl_delete_store", { item_ids: item_ids, multiple: true }, function(_response) {

                if (_response.success) {
                  atoastr.success(_response.msg);
                  table.fnDraw();
                  return;
                } else {
                  atoastr.error((_response.error || ASL_REMOTE.LANG.error_try_again));
                  return;
                }

              }, 'json');
            }
          );
      });

      //Change the Status
      $('#btn-change-status').bind('click', function(e) {

        var $tmp_stores = $('.asl-p-cont .table input:checked');

        if ($tmp_stores.length == 0) {
          atoastr.error('No Store Selected');
          return;
        }

        var item_ids = [];
        $('.asl-p-cont .table input:checked').each(function(i) {

          item_ids.push($(this).attr('data-id'));
        });


        ServerCall(ASL_REMOTE.URL + "?action=asl_store_status", { item_ids: item_ids, multiple: true, status: $('#asl-ddl-status').val() }, function(_response) {

          if (_response.success) {
            atoastr.success(_response.msg);
            table.fnDraw();
            return;
          } else {
            atoastr.error((_response.error || ASL_REMOTE.LANG.error_try_again));
            return;
          }

        }, 'json');
      });

      //show delete store model
      $('#tbl_stores tbody').on('click', '.glyphicon-trash', function(e) {

        var _store_id = $(this).attr("data-id");

        aswal({
          title: ASL_REMOTE.LANG.delete_store,
          text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.delete_store + " " + _store_id + "?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: ASL_REMOTE.LANG.delete_it,
        }).then(function() {

          ServerCall(ASL_REMOTE.URL + "?action=asl_delete_store", { store_id: _store_id }, function(_response) {

            if (_response.success) {
              atoastr.success(_response.msg);
              table.fnDraw();
              return;
            } else {
              atoastr.error((_response.error || ASL_REMOTE.LANG.error_try_again));
              return;
            }

          }, 'json');

        });
      });


      $("thead input").keyup(function(e) {

        if (e.keyCode == 13) {
          table.fnDraw();
        }
      });


      /*$("#Search_Data").click( function () {
      	table.fnDraw();
      });*/
    },
    customize_map: function(_asl_map_customize) {

      //RESET
      var trafic_layer, transit_layer, bike_layer;
      $('#frm-asl-layers')[0].reset();


      //init the maps
      if (!(window['google'] && google.maps)) {
        map_object.intialize();
        //drawing_instance.initialize();
      } else
        asl_map_intialized();

      window['asl_map_intialized'] = function() {

        map_object.render_a_map(asl_configs.default_lat, asl_configs.default_lng);
        asl_drawing.initialize(map_object.map_instance);


        //ADd trafice layer
        if (_asl_map_customize.trafic_layer && _asl_map_customize.trafic_layer == 1) {

          $('#asl-trafic_layer')[0].checked = true;

          trafic_layer = new google.maps.TrafficLayer();
          trafic_layer.setMap(map_object.map_instance);
        }


        //ADd bike layer
        if (_asl_map_customize.bike_layer && _asl_map_customize.bike_layer == 1) {

          $('#asl-bike_layer')[0].checked = true;

          bike_layer = new google.maps.BicyclingLayer();
          bike_layer.setMap(map_object.map_instance);
        }

        //ADd transit layer
        if (_asl_map_customize.transit_layer && _asl_map_customize.transit_layer == 1) {

          $('#asl-transit_layer')[0].checked = true;

          transit_layer = new google.maps.TransitLayer();
          transit_layer.setMap(map_object.map_instance);
        }

        //ADd transit layer
        if (_asl_map_customize.marker_animations && _asl_map_customize.marker_animations == 1) {

          $('#asl-marker_animations')[0].checked = true;
        }


        ///Load the DATA
        if (_asl_map_customize.drawing) {

          asl_drawing.loadData(_asl_map_customize.drawing);
        }
      };


      //Trafic Layer
      $('.asl-p-cont .map-option-bottom #asl-trafic_layer').bind('click', function(e) {

        if (this.checked) {

          trafic_layer = new google.maps.TrafficLayer();
          trafic_layer.setMap(map_object.map_instance);
        } else
          trafic_layer.setMap(null);

      });

      //Transit Layer
      $('.asl-p-cont .map-option-bottom #asl-transit_layer').bind('click', function(e) {

        if (this.checked) {

          transit_layer = new google.maps.TransitLayer();
          transit_layer.setMap(map_object.map_instance);
        } else
          transit_layer.setMap(null);
      });

      //Bike Layer
      $('.asl-p-cont .map-option-bottom #asl-bike_layer').bind('click', function(e) {

        if (this.checked) {

          bike_layer = new google.maps.BicyclingLayer();
          bike_layer.setMap(map_object.map_instance);
        } else
          bike_layer.setMap(null);

      });

      //Marker Animate
      $('.asl-p-cont .map-option-bottom #asl-marker_animations').bind('click', function(e) {

        if (this.checked) {
          map_object.map_marker.setAnimation(google.maps.Animation.Xp);
        }
      });


      //Save the Map Customization
      $('#asl-save-map').bind('click', function(e) {

        var $btn = $(this);

        var layers = {
          trafic_layer: ($('#asl-trafic_layer')[0].checked) ? 1 : 0,
          transit_layer: ($('#asl-transit_layer')[0].checked) ? 1 : 0,
          bike_layer: ($('#asl-bike_layer')[0].checked) ? 1 : 0,
          marker_animations: ($('#asl-marker_animations')[0].checked) ? 1 : 0,
          drawing: asl_drawing.get_data()
        };

        $btn.bootButton('reset');
        ServerCall(ASL_REMOTE.URL, { action: 'asl_save_custom_map', data_map: JSON.stringify(layers) }, function(_response) {

          $btn.bootButton('reset');

          if (_response.success) {

            atoastr.success(_response.msg);
            return;
          } else
            atoastr.error(_response.error);
        }, 'json');

      });

    },
    InfoBox_maker: function(_inbox_id) {


      var $form = $('#frm-Info-box');

      //RESET
      $('#asl-infobox').val(String(_inbox_id));

      //ASL info box change
      $('#asl-infobox').bind('change', function(e) {

        var _URL = window.location.href.replace(/&infobox_id=\d+/, '');

        _URL += '&infobox_id=' + this.value;
        window.location.href = _URL;
      });



      $('#asl-save-infobox').bind('click', function(e) {

        var $btn = $(this);

        var content_ = (tinymce.get('asl-p-cont')) ? tinymce.get('asl-p-cont').getContent() : $('#asl-p-cont').val()

        var formData = { 'asl-p-cont': content_, 'asl-infobox': $('#asl-infobox').val() };
        formData['action'] = 'asl_save_infobox';

        $btn.bootButton('loading');
        ServerCall(ASL_REMOTE.URL, formData, function(_response) {

          $btn.bootButton('reset');

          if (_response.success) {

            atoastr.success(_response.msg);
            return;
          } else
            atoastr.error(_response.error);
        }, 'json');
      });

    },
    edit_store: function(_store) {

      this.add_store(true, _store);
    },
    add_store: function(_is_edit, _store) {

      var $form = $('#frm-addstore'),
        hdlr = this;


      var current_time = new Date();
      current_time.setHours(7);
      current_time.setMinutes(0);

      var start_time = current_time.toLocaleTimeString(navigator.language, { hour: '2-digit', minute: '2-digit' });
      current_time.setHours(current_time.getHours() + 12);

      var end_time = current_time.toLocaleTimeString(navigator.language, { hour: '2-digit', minute: '2-digit' });




      /*Dropdown Style*/
      if (_store && _store.logo_id)
        $('#ddl-asl-logos').val(String(_store.logo_id));



      $('#ddl-asl-logos').ddslick({
        //data: ddData,
        imagePosition: "right",
        selectText: ASL_REMOTE.LANG.select_logo,
        truncateDescription: true
        //defaultSelectedIndex:(_store)?String(_store.logo_id):null
      });

      if (_store && _store.marker_id)
        $('#ddl-asl-markers').val(String(_store.marker_id));

      $('#ddl-asl-markers').ddslick({
        //data: ddData,
        imagePosition: "right",
        selectText: ASL_REMOTE.LANG.select_marker,
        truncateDescription: true
        //    defaultSelectedIndex: (_store)?String(_store.marker_id):null
      });

      var current_date = new Date();

      //Add/Remove DateTime Picker
      $('.asl-time-details tbody').on('click', '.add-k-add', function(e) {

        var $new_slot = $('<div class="form-group">\
										<div class="input-group bootstrap-asltimepicker">\
								          <input type="text" value="9:30 AM" class="form-control asltimepicker validate[required,funcCall[ASLmatchTime]]" placeholder="' + ASL_REMOTE.LANG.start_time + '">\
								          <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>\
								        </div>\
								        <div class="input-append input-group bootstrap-asltimepicker">\
								          <input type="text" value="6:30 PM" class="form-control asltimepicker validate[required]" placeholder="' + ASL_REMOTE.LANG.end_time + '">\
								          <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>\
								        </div>\
								        <span class="add-k-delete glyp-trash">\
				                	<svg width="16" height="16"><use xlink:href="#i-trash"></use></svg>\
				                </span>\
								    </div>');

        //var $cur_slot   = $(this).parent();//.find('.asl-all-day-times .asl-closed-lbl');
        var $cur_slot = $(this).parent().prev().find('.asl-all-day-times .asl-closed-lbl');
        $cur_slot.before($new_slot);


        $new_slot.find('input.asltimepicker').removeAttr('id').attr('class', 'form-control asltimepicker validate[required]').val('').asltimepicker({
          defaultTime: current_date,
          //orientation: 'auto',
          showMeridian: (asl_configs && asl_configs.time_format == '1') ? false : true,
          appendWidgetTo: '.asl-p-cont'
        });
      });

      $('.asl-time-details tbody').on('click', '.add-k-delete', function(e) {

        var $this_tr = $(this).parent().remove();
      });

      //$('.asl-p-cont .asl_dates').datepicker();


      $('.asl-p-cont .asl-time-details .asltimepicker').asltimepicker({
        //defaultTime: current_date,
        //orientation: 'auto',
        showMeridian: (asl_configs && asl_configs.time_format == '1') ? false : true,
        appendWidgetTo: '.asl-p-cont'
      });

      //Convert the time for validation
      function asl_timeConvert(_str) {

        if (!_str) return 0;

        var time = $.trim(_str).toUpperCase();

        //when 24 hours
        if (asl_configs && asl_configs.time_format == '1') {

          var regex = /(1[012]|[0-9]):[0-5][0-9]/;

          if (!regex.test(time))
            return 0;

          var hours = Number(time.match(/^(\d+)/)[1]);
          var minutes = Number(time.match(/:(\d+)/)[1]);

          return hours + (minutes / 100);
        } else {

          var regex = /(1[012]|[1-9]):[0-5][0-9][ ]?(AM|PM)/;

          if (!regex.test(time))
            return 0;

          var hours = Number(time.match(/^(\d+)/)[1]);
          var minutes = Number(time.match(/:(\d+)/)[1]);
          var AMPM = (time.indexOf('PM') != -1) ? 'PM' : 'AM';

          if (AMPM == "PM" && hours < 12) hours = hours + 12;
          if (AMPM == "AM" && hours == 12) hours = hours - 12;

          return hours + (minutes / 100);
        }
      };

      //Match the Date :: validation
      /*
      window['ASLmatchDate'] = function(field, rules, i, options) {

      	var field_2 	= field.parent().parent().parent().next().find('.asl-datepicker'),
      		date_val_1 	= new Date(field.val()).getTime(),
      		date_val_2 	= new Date(field_2.val()).getTime();
      	
      	if(date_val_1 >= date_val_2)
      		return "* Invalid";
      };
      */

      //Match the time :: validation
      window['ASLmatchTime'] = function(field, rules, i, options) {

        var field_2 = field.parent().next().children(0),
          time_val_1 = asl_timeConvert(field.val()),
          time_val_2 = asl_timeConvert(field_2.val());


        if (time_val_1 >= time_val_2)
          return "* Invalid";
      };

      //init the maps
      $(function() {


        if (!(window['google'] && google.maps)) {
          map_object.intialize();
        } else
          asl_map_intialized();
      });

      window['asl_map_intialized'] = function() {
        if (_store)
          map_object.render_a_map(_store.lat, _store.lng);
        else
          map_object.render_a_map(parseFloat(asl_configs.default_lat), parseFloat(asl_configs.default_lng));
      };

      
      //the category ddl
      $('#ddl_categories').chosen({
        width: "100%",
        placeholder_text_multiple: ASL_REMOTE.LANG.select_category,
        no_results_text: ASL_REMOTE.LANG.no_category
        /*
        allow_single_deselect:true,
        disable_search_threshold:10*/
      });

      /*Form Submit*/
      $form.validationEngine({
        binded: true,
        scroll: false
      });

      //To get Lat/lng
      $('#txt_city,#txt_state,#txt_postal_code').bind('blur', function(e) {

        if (!isEmpty($form[0].elements["data[city]"].value) && !isEmpty($form[0].elements["data[postal_code]"].value)) {

          var address = [$form[0].elements["data[street]"].value, $form[0].elements["data[city]"].value, $form[0].elements["data[postal_code]"].value, $form[0].elements["data[state]"].value];

          var q_address = [];

          for (var i = 0; i < address.length; i++) {

            if (address[i])
              q_address.push(address[i]);
          }

          var _country = jQuery('#txt_country option:selected').text();

          //Add country if available
          if (_country && _country != ASL_REMOTE.LANG.select_country) {
            q_address.push(_country);
          }

          address = q_address.join(', ');

          codeAddress(address, function(_geometry) {

            var s_location = [_geometry.location.lat(), _geometry.location.lng()];
            var loc = new google.maps.LatLng(s_location[0], s_location[1]);
            map_object.map_marker.setPosition(_geometry.location);
            map.panTo(_geometry.location);
            app_engine.pages.store_changed(s_location);

          });
        }
      });


      //Coordinates Fixes
      var _coords = {
        lat: '',
        lng: ''
      };

      $('#lnk-edit-coord').bind('click', function(e) {

        _coords.lat = $('#asl_txt_lat').val();
        _coords.lng = $('#asl_txt_lng').val();

        $('#asl_txt_lat,#asl_txt_lng').val('').removeAttr('readonly');
      });

      var $coord = $('#asl_txt_lat,#asl_txt_lng');
      $coord.bind('change', function(e) {

        if ($coord[0].value && $coord[1].value && !isNaN($coord[0].value) && !isNaN($coord[1].value)) {

          var loc = new google.maps.LatLng(parseFloat($('#asl_txt_lat').val()), parseFloat($('#asl_txt_lng').val()));
          map_object.map_marker.setPosition(loc);
          map.panTo(loc);
        }
      });

      //Get Working Hours
      function getOpenHours() {

        var open_hours = {};

        $('.asl-time-details .asl-all-day-times').each(function(e) {

          var $day = $(this),
            day_index = String($day.data('day'));
          open_hours[day_index] = null;

          if ($day.find('.form-group').length > 0) {

            open_hours[day_index] = [];
          } else {

            open_hours[day_index] = ($day.find('.asl-closed-lbl input')[0].checked) ? '1' : '0';
          }

          $day.find('.form-group').each(function() {

            var $hours = $(this).find('input');
            open_hours[day_index].push($hours.eq(0).val() + ' - ' + $hours.eq(1).val());
          });

        });

        return JSON.stringify(open_hours);
      }

      //Add store button
      $('#btn-asl-add').bind('click', function(e) {

        if (!$form.validationEngine('validate')) return;

        var $btn    = $(this),
          formData  = $form.serializeObject();

        formData['action'] = (_is_edit) ? 'asl_edit_store' : 'asl_add_store';
        formData['category'] = $('#ddl_categories').val();

        if (_is_edit) { formData['updateid'] = $('#update_id').val(); }

        formData['data[marker_id]'] = ($('#ddl-asl-markers').data('ddslick').selectedData) ? $('#ddl-asl-markers').data('ddslick').selectedData.value : jQuery('#ddl-asl-markers .dd-selected-value').val();
        formData['data[logo_id]'] = ($('#ddl-asl-logos').data('ddslick').selectedData) ? $('#ddl-asl-logos').data('ddslick').selectedData.value : jQuery('#ddl-asl-logos .dd-selected-value').val();

        //Ordering
        if (formData['ordr'] && isNaN(formData['ordr']))
          formData['ordr'] = '0';

        /*
                var _open_days = $('.asl-p-cont #asl-open_days').val();

                _open_days = (_open_days)?_open_days.join(','):'';
                formData['data[days]'] = _open_days;
                	
				
                //time per day
                formData['data[time_per_day]'] = (formData['data[time_per_day]'])?1:0;
                */

        formData['data[open_hours]'] = getOpenHours();


        $btn.bootButton('loading');
        ServerCall(ASL_REMOTE.URL, formData, function(_response) {

          $btn.bootButton('reset');
          if (_response.success) {

            $form[0].reset();
            $btn.bootButton('completed');

            if (_is_edit) {
              _response.msg += " Redirect...";
              window.location.replace(ASL_REMOTE.URL.replace('-ajax', '') + "?page=manage-agile-store");
            }
            /*
            else
            	$('.days_table').addClass('hide');
            */

            atoastr.success(_response.msg);
            return;
          } else if (_response.error) {
            atoastr.error(_response.error);
            return;
          } else {
            atoastr.error(ASL_REMOTE.LANG.error_try_again);
          }
        }, 'json');
      });


      //UPLOAD LOGO FILE IMAGE
      var url_to_upload = ASL_REMOTE.URL,
          $form_upload  = $('#frm-upload-logo');


      app_engine.uploader($form_upload, url_to_upload + '?action=asl_upload_logo', function(_e, _data) {

        var data = _data.result;

        if (!data.success) {
          atoastr.error(data.msg);
        } else {

          var _HTML = '';
          for (var k in data.list)
            _HTML += '<option data-imagesrc="' + ASL_Instance.url + 'public/Logo/' + data.list[k].path + '" data-description="&nbsp;" value="' + data.list[k].id + '">' + data.list[k].name + '</option>';


          $('#ddl-asl-logos').empty().ddslick('destroy');
          $('#ddl-asl-logos').html(_HTML).ddslick({
            //data: ddData,
            imagePosition: "right",
            selectText: "Select Logo",
            truncateDescription: true,
            defaultSelectedIndex: (_store) ? String(_store.logo_id) : null
          });

          $('#addimagemodel').smodal('hide');
          $form_upload.find('.progress_bar_').hide();
          $form_upload.find('input:text, input:file').val('');
        }
      });


      //UPLOAD MARKER IMAGE FILE
      var $form_marker = $('#frm-upload-marker');


      app_engine.uploader($form_marker, url_to_upload + '?action=asl_upload_marker', function(_e, _data) {

        var data = _data.result;

        if (!data.success) {

          atoastr.error(data.msg);
        } else {

          var _HTML = '';
          for (var k in data.list)
            _HTML += '<option data-imagesrc="' + ASL_Instance.url + 'public/icon/' + data.list[k].icon + '" data-description="&nbsp;" value="' + data.list[k].id + '">' + data.list[k].marker_name + '</option>';


          $('#ddl-asl-markers').empty().ddslick('destroy');

          $('#ddl-asl-markers').html(_HTML).ddslick({
            //data: ddData,
            imagePosition: "right",
            selectText: "Select marker",
            truncateDescription: true,
            defaultSelectedIndex: (_store) ? String(_store.marker_id) : null
          });

          $('#addmarkermodel').smodal('hide');
          $form_marker.find('.progress_bar_').hide();
          $form_marker.find('input:text, input:file').val('');
        }
      });

    },
    /**
     * [user_setting User Settings]
     * @param  {[type]} _configs [description]
     * @return {[type]}          [description]
     */
    user_setting: function(_configs) {

      var $form = $('#frm-usersetting');

      var _keys = Object.keys(_configs);


      /**
       * [set_tmpl_image Current Image Template]
       */
      function set_tmpl_image() {

        var _tmpl = document.getElementById('asl-template').value,
          _lyout = document.getElementById('asl-layout').value;

        $(document.getElementById('asl-tmpl-img')).attr('src', ASL_Instance.url + 'admin/images/' + 'asl-tmpl-' + _tmpl + '-' + _lyout + '.png');
      }

      for (var i in _keys) {


        if (!_keys.hasOwnProperty(i)) continue;

        if ( /*_keys[i] == 'layout' || */ _keys[i] == 'map_layout' || _keys[i] == 'infobox_layout' || _keys[i] == 'color_scheme' || _keys[i] == 'color_scheme_1' || _keys[i] == 'color_scheme_2' || _keys[i] == 'font_color_scheme') {

          
          var $elem = $form.find('#asl-' + _keys[i] + '-' + _configs[_keys[i]]);
          $elem[0].checked = true;
          continue;
        }


        var $elem = $form.find('#asl-' + _keys[i]);


        if ($elem[0].type == 'checkbox')
          $elem[0].checked = (_configs[_keys[i]] == '0') ? false : true;
        else
          $elem.val(_configs[_keys[i]]);
      }


      ///Make layout Active
      $('.asl-p-cont .layout-box img').eq($('#asl-template')[0].selectedIndex).addClass('active');

      $('#asl-template').bind('change', function(e) {

        $('.asl-p-cont .layout-box img.active').removeClass('active');
        $('.asl-p-cont .layout-box img').eq(this.selectedIndex).addClass('active');
      });

      /////*Validation Engine*/////
      $form.validationEngine({
        binded: true,
        scroll: false
      });


      $('#btn-asl-user_setting').bind('click', function(e) {

        if (!$form.validationEngine('validate')) return;

        var $btn = $(this);

        $btn.bootButton('loading');

        var all_data = {
          data: {
            show_categories: 0,
            advance_filter: 0,
            time_switch: 0,
            category_marker: 0,
            distance_slider: 0,
            analytics: 0,
            additional_info: 0,
            scroll_wheel: 0,
            target_blank: 0,
            user_center: 0,
            smooth_pan: 0,
            sort_by_bound: 0,
            full_width: 0,
            //filter_result:0,
            radius_circle: 0,
            remove_maps_script: 0
            //range_slider:0
          }
        };

        var data = $form.serializeObject();

        
        all_data = $.extend(all_data, data);

        //	Save the custom Map
        all_data['map_style'] = document.getElementById('asl-map_layout_custom').value;


        ServerCall(ASL_REMOTE.URL + '?action=asl_save_setting', all_data, function(_response) {

          $btn.bootButton('reset');

          if (_response.success) {
            atoastr.success(_response.msg);
            return;
          } else if (_response.error) {

            atoastr.error(_response.msg);
            return;
          } else {
            atoastr.success('Error Occurred.');
            return;

          }
        }, 'json');
      });

      if (isEmpty(_configs['template']))
        _configs['template'] = '0';

      //Show the option of right template
      $('.box_layout_' + _configs['template']).removeClass('hide');

      $('.asl-p-cont #asl-layout').bind('change', function(e) {

        set_tmpl_image();

      });

      $('.asl-p-cont #asl-template').bind('change', function(e) {

        var _value = this.value;
        $('.asl-p-cont .template-box').addClass('hide');
        $('.box_layout_' + _value).removeClass('hide');
        set_tmpl_image();

      });

      set_tmpl_image();
    },
    import_store: function() {

      //	Validate the Plugin
      this._validate_page();

      /*Validate API Key*/
      $('#btn-validate-key').bind('click', function(e) {

        var $this = $(this);

        $this.bootButton('loading');

        ServerCall(ASL_REMOTE.URL + '?action=asl_validate_api_key', {}, function(_response) {

          $this.bootButton('reset');

          if (_response.success) {

            atoastr.success(_response.msg);
          } else
            atoastr.error(_response.msg);

        }, 'json');

      });


      /*Fetch the Missing Coordinates*/
      $('#btn-fetch-miss-coords').bind('click', function(e) {

        var $this = $(this);
        $this.bootButton('loading');

        ServerCall(ASL_REMOTE.URL + '?action=asl_fill_missing_coords', {}, function(_response) {

          $this.bootButton('reset');

          if (_response.success) {

            // making summary						
            var warning_summary = "<ul>";

            for (var _s in _response.summary) {

              warning_summary += "<li>" + _response.summary[_s] + "</li>";
            }

            warning_summary += '</ul>';

            console.log('===> jscript.js ===> 2314', warning_summary);
            $('#message_complete').html("<div class='alert alert-info'>" + warning_summary + "</div>");
            atoastr.success(_response.msg);
            return;
          }
          else
            atoastr.error(_response.msg);


        }, 'json');

      });

      /*Delete Stores*/
      var _delete_all_stores = function() {

        var $this = $('#asl-delete-stores');
        $this.bootButton('loading');

        ServerCall(ASL_REMOTE.URL + '?action=asl_delete_all_stores', {}, function(_response) {

          $this.bootButton('reset');
          atoastr.success(_response.msg);
        }, 'json');
      };


      /*Delete All stores*/
      $('#asl-delete-stores').bind('click', function(e) {

        aswal({
          title: ASL_REMOTE.LANG.delete_all_stores,
          text: ASL_REMOTE.LANG.warn_question + ' ' + ASL_REMOTE.LANG.delete_all_stores + "?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: ASL_REMOTE.LANG.delete_all
        }).then(
          function() {

            _delete_all_stores();
          }
        );
      });


      //import store form xlsx file
      $('.btn-asl-import_store').bind('click', function(e) {

        var $this = $(this);
        $this.bootButton('loading');

        var _params = { use_key: (document.getElementById('asl-use-key').checked) ? 1 : 0, create_category: (document.getElementById('asl-create-category').checked) ? 1 : 0, data_: $(this).attr('data-id') };

        ServerCall(ASL_REMOTE.URL + '?action=asl_import_store', _params, function(_response) {

          $this.bootButton('reset');
          if (_response.success) {

            // making summary						
            var warning_summary = "<ul>";

            for (var _s in _response.summary) {

              warning_summary += "<li>" + _response.summary[_s] + "</li>";
            }

            warning_summary += '</ul>';

            var _color = (_response.imported_rows == 0) ? 'error' : 'success';
            atoastr[_color](_response.imported_rows + " Rows Import");
            $('#message_complete').html("<div class='alert alert-warning'>" + warning_summary + "</div>");
            return;
          }
        }, 'json');

      });

      //delete import file
      $('.btn-asl-delete_import_file').bind('click', function(e) {


        ServerCall(ASL_REMOTE.URL + '?action=asl_delete_import_file', { data_: $(this).attr('data-id') }, function(_response) {


          if (_response.success) {

            atoastr.success(_response.msg);
            window.location.replace(ASL_REMOTE.URL.replace('-ajax', '') + "?page=import-store-list");
            return;
          } else if (_response.error) {

            atoastr.error(_response.error);
            return;
          } else {

            atoastr.error('Error deleting file.');
            return;

          }
        }, 'json');

      });

      //export file

      $('#export_store_file_').bind('click', function(e) {


        ServerCall(ASL_REMOTE.URL + '?action=asl_export_file', { data_: "", with_id: (document.getElementById('asl-export-ids').checked) ? 1 : 0 }, function(_response) {

          if (_response.success) {

            window.location = _response.msg;
            return;
          } else if (_response.error) {

            atoastr.error(_response.error);
            return;
          } else {

            atoastr.success(_response.msg);
            return;

          }


        }, 'json');

      });

      //upload import file
      var url_to_upload   = ASL_REMOTE.URL,
          $form_upload    = $('#import_store_file');

      app_engine.uploader($form_upload, url_to_upload + '?action=asl_upload_store_import_file', function(_e, _data) {

        var data = _data.result;

        if (!data.success) {

          atoastr.error(data.msg);
        }
        else {
          atoastr.success(data.msg);
          $('#import_store_file_emodel').smodal('hide');
          $('#progress_bar_').hide();
          $('#frm-upload-logo').find('input:text, input:file').val('');
          window.location.replace(ASL_REMOTE.URL.replace('-ajax', '') + "?page=import-store-list");
        }
      });
    }
  };

  //<p class="message alert alert-danger static" style="display: block;">Legal Location not found<button data-dismiss="alert" class="close" type="button"> ×</button><span class="block-arrow bottom"><span></span></span></p>
  //if jquery is defined
  if ($)
    $('.asl-p-cont').append('<div class="loading site hide">Working ...</div><div class="asl-dumper dump-message"></div>');

})(jQuery, asl_engine);