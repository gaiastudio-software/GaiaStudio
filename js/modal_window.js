// file:	modal_window.js

$ (document).keyup (function (e)
{
	if (e.keyCode == 13) { // MAGIC
		$ ('#mask-iframe').hide();
		$ ('.window').hide();
	}
});

$ (document).ready (function()
{
	$ (window).resize (function()
	{
		var box = $ ('#boxes-div .window');
		var maskHeight = $ (document).height();
		var maskWidth = $ (window).width();

		$ ('#mask-iframe').css ({
			'width' : maskWidth,
			'height' : maskHeight
		});

		var winH = $ (window).height();
		var winW = $ (window).width();

		box.css ('top', winH / 2 - box.height() / 2);
		box.css ('left', winW / 2 - box.width() / 2);
	});

	$ ('a[name=modal-anchor]').click (function (e)
	{
		e.preventDefault();
		var id = $ (this).attr ('href');

		var maskHeight = $ (document).height();
		var maskWidth = $ (window).width();

		$ ('#mask-iframe').css ({
			'width' : maskWidth,
			'height' : maskHeight
		});

		$ ('#mask-iframe').fadeIn (1000);
		$ ('#mask-iframe').fadeTo ("slow", 0.8);

		var winH = $ (window).height();
		var winW = $ (window).width();

		$ (id).css ('top', winH / 2 - $ (id).height() / 2);
		$ (id).css ('left', winW / 2 - $ (id).width() / 2);

		$ (id).fadeIn (2000);
	});

	$ ('.window .close').click (function (e) {
		e.preventDefault();
		$ ('#mask-iframe, .window').hide();
	});

	$ ('#mask-iframe').click (function() {
		$ (this).hide();
		$ ('.window').hide();
	});
});
