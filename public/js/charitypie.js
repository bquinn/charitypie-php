	/**
	 * Very important that min value of a slider = 1, as 0 causes a bug in the jQuery core
	 */
	getChart = function() {
		charity = "";
		value = "";
		$(".slider").each(function(){
			if ($(this).slider("value") > 1) {
			  if (charity > "") charity += "|"
			  charity += $(this).prev().text();
			  if (value > "") value += ",";
			  value += $(this).slider("value");
			}
		});

		if (value=="") {
		  charity = "None";
		  value = "100";
		}
		
		charity = escape(charity);

		$('#chart img').attr('src','http://chart.apis.google.com/chart?cht=p&chd=t:'+value+'&chs=500x100&chl='+charity);
	}

	addSlider = function(charity) {
		$("<p>"+charity.text()+"</p>").click(function(e){
			removeSlider(e.target);
		}).appendTo('#demo');

		$('<div class="slider" style="width:200px;"></div>').slider({
			orientation: "horizontal",
			range: "min",
			min: 1,
			max: 100,
			value: 60,
			stop: function(event, ui) {
				getChart();
			}
		}).appendTo('#demo');

		$(charity).remove();

		getChart();
	}

	removeSlider = function(charity) {
		listitem = $('<li>'+$(charity).text()+'</li>').click(function(e){
				addSlider($(e.target))
			});
		$('#charity-list').append(listitem);
		$(charity).next().remove().end().remove();
		getChart();
	}

	$(function() {
		$("#charity-list li").each(function(){
			$(this).click(function(e){
		  	addSlider($(e.target));
		  	});
		  });
	});

function findSWF(movieName) {
  if (navigator.appName.indexOf("Microsoft")!= -1) {
    return window[movieName];
  } else {
    return document[movieName];
  }
}

var pie_change_message;
function insertChangedPieMessage() {
  notice = $('.pie-control').find('.notice');
  if (notice.length == 0) {
    notice = $('<div class="notice"></div>');
    mssg = $('<p></p>').html(pie_change_message);
    notice = $(notice).append(mssg).css('display','none');
    $('.pie-control').prepend(notice);
    $(notice).show('fast');
  }
}

function update_pie()
{
  chart_data = data;

  var total = 0
  for (var item in chart_data['elements'][0]['values']) {
    val = parseFloat($('#slice-'+chart_data['elements'][0]['values'][item]['id']).val());
    total = total+val
  }

  for (var item in chart_data['elements'][0]['values']) {
    val = $('#slice-'+chart_data['elements'][0]['values'][item]['id']).val();
    chart_data['elements'][0]['values'][item]['value'] = val/total*100;
  }

  data = chart_data;
  
	//
	// pass the new chart_data as a JSON string to the chart:
	//
	tmp = findSWF("my_chart");
    x = tmp.load( JSON.stringify(chart_data) );
}
	
$(function() {
$("#pie_slices .slice-size").each(function(){
  var value = $(this).val();
  var div = $('<div></div>');
  $(div).slider({
    orientation: "horizontal",
    range: "min",
    min: 1,
    max: 100,
    value: value,
    stop: function(event, ui) {
      slices = $('#pie_slices').serializeArray();
      $.post($('#pie_slices').attr('action'),slices);
      insertChangedPieMessage();
    },
    slide: function(event, ui) {
      $(event.target).prev().val(ui.value);
      update_pie();
    }
  });
  $(this).after(div).css('display','none');
})
$("#pie_slices input[type=submit]").remove();
});