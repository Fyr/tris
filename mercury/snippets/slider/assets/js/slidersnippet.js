var MercurySlider = function(obj, autoplay) {
	var self = this;
		self._getter = {
			Scene: ".scene",
			
			Controls: ".controls .button",
			
			Items: ".scene .item",
			Images: ".scene .item img"
		},
		self._objects = {},
		self._buffer = {
			timer: null,
			
			active: false,
			hiding: false,
			
			index: 0,
			countPages : 0,
			
			page : 0,
			oldPage : 0,
			
			itemsOnPage: 1
		},
		this.controlsClick = function (event) {
			event.preventDefault();
			event.stopPropagation();
			
			$this = $(this);

			if (!self._buffer.active) {
				if ($this.hasClass('right')) {
					self.changePage(1);
				}
				if ($this.hasClass('left')) {
					self.changePage(-1);
				}
			}
		},
		this.setOptions = function (autoplay) {
			self._buffer.autoplay = autoplay;
			if (autoplay) self._buffer.autoplay = (self._objects.Start.data('autoplay') == false) ? false : true;
			
			self._buffer.pageTime = self._objects.Start.data('slidetime') ? self._objects.Start.data('slidetime') : 7000;
			self._buffer.slideTime = self._objects.Start.data('pagetime') ? self._objects.Start.data('pagetime') : 800;
			
			self._buffer.width = self._objects.Start.data('slider_width') ? self._objects.Start.data('slider_width') : 50;
			self._buffer.height = self._objects.Start.data('slider_height') ? self._objects.Start.data('slider_height') : 200;
				
			self._buffer.images = [];

			self._objects.Images.each(
				function () {
					img = this;
					imgObj = {
						obj: $(img).closest('.item'),
						url: $(img).attr('src'),
						naturalWidth: img.naturalWidth,
						naturalHeight: img.naturalHeight
					}
					self._buffer.images.push(imgObj);
				}
			);
			
			self._objects.Images.remove();
			
			oldStyle = self._objects.Start.attr('style');
			
			propertyStart0 = {
				'style': 'width: '+self._buffer.width+'%; '+oldStyle
			}			
			self._objects.Start.parent().attr(propertyStart0);
			
			propertyStart1 = {
				'style': 'width: 100%;'
			}			
			self._objects.Start.attr(propertyStart1);
			
			propertyStart2 = {
				'style': 'width: 100%; height: '+ self._buffer.height+'px;'
			}
			self._objects.Start.attr(propertyStart2);
			
			self._buffer.scene = {
				width : self._objects.Start.innerWidth(),
				height : self._buffer.height
			}
			
			var k = 0;
			
			for (var i in self._buffer.images) {
				k = self._buffer.scene.width / self._buffer.images[i].naturalWidth;
				if (k > 1) {
					 self._buffer.images[i].width = self._buffer.images[i].naturalWidth * k + 44;
					 self._buffer.images[i].height = self._buffer.images[i].naturalHeight * k + 44;
					 self._buffer.images[i].styleObj = {
						'style' : 'width: 100%; height: '+ self._buffer.images[i].height+'px; background: url('+ self._buffer.images[i].url+') center center no-repeat; background-size: cover;'
					}					
				} else if ((k>0) && (k<=1)){
					 self._buffer.images[i].width =  self._buffer.images[i].naturalWidth * k + 44;
					 self._buffer.images[i].height =  self._buffer.images[i].naturalHeight * k + 44;
					 self._buffer.images[i].styleObj = {
						'style' : 'width: 100%; background: url('+ self._buffer.images[i].url+') center center no-repeat; background-size: cover;'
					}					
				} else return false

				$( self._buffer.images[i].obj).attr( self._buffer.images[i].styleObj);
			}
			return true;
		},
		this.stop = function () {
			if (self._buffer.autoplay) clearInterval( self._buffer.timer);
			self.showControls();
		},
		this.play = function () {
			if (self._buffer.autoplay) self._buffer.timer = setInterval(
				function () {
					self.changePage(1);
				},
				self._buffer.pageTime
			);
			self.hideControls();			
		},
		this.showControls = function () {
			if (self._buffer.hiding) self._objects.Controls.stop(true, false);
			self._buffer.hiding = true;
			self._objects.Controls.animate(
				{'opacity': 0.5},
				800,
				function () {
					self._buffer.hiding = false;
				}
			);
		},
		this.hideControls = function () {
			if (self._buffer.hiding) self._objects.Controls.stop(true, false);
			self._buffer.hiding = true;
			self._objects.Controls.animate(
				{'opacity': 0},
				400,
				function () {
					self._buffer.hiding = false;
				}
			);
		}		
		this.changePage = function (index) {
			 self._buffer.active = true;

			 self._buffer.index = index;

			 self._buffer.oldPage =  self._buffer.page;
			 self._buffer.page = ( self._buffer.countPages + ( self._buffer.page +  self._buffer.index)) %  self._buffer.countPages;
			
			$(self._objects.Items[ self._buffer.oldPage]).css(
				{'z-index':'1'}
			).animate(
				{'opacity': '0'},
				 self._buffer.slideTime
			).removeClass('active');
			
			$(self._objects.Items[ self._buffer.page]).css(
				{'z-index':'60'}
			).animate(
				{'opacity': '1'},
				 self._buffer.slideTime,
				function () { 
					 self._buffer.active = false;
				}
			).addClass('active');			
		},
		this.init = function (obj, autoplay) {
			
			if (obj.hasClass('initialized')) return false;
			obj.removeClass('uninitialized').addClass('initialized');
		
			self._objects.Start = obj;
			
			if (!self._objects.Start) return false;

			for (var key in self._getter)
				if (key != 'Start')
					self._objects[key] = self._objects.Start.find(self._getter[key]);

			self._objects.Controls.on("click", self.controlsClick);
			
			self._objects.Start.on("mouseenter", self.stop);
			self._objects.Start.on("mouseleave", self.play);
			
			self._buffer.countPages = Math.ceil(self._objects.Items.length /  self._buffer.itemsOnPage);
			
			if(!self.setOptions(autoplay)) return false;
			self.play();
			
			$(self._objects.Items[0]).css(
				{
				'z-index':'60',
				'opacity':'1'
				}
			);
			self.hideControls();
			return this;
		};
	this.init(obj, autoplay);
};
var MercurySliders = function() {
	var self = this;
		self._objects = {};
		this.init = function () {
			self._objects = $('.mercury_slider');
			self._objects.Sliders = [];
			
			if (self._objects.length) {
				self._objects.each(
					function () {
						var item = new MercurySlider($(this), true);
						self._objects.Sliders.push(item);
					}
				);
			}
		}
	self.init();
};

$(function () {
	var gzwSliders = new MercurySliders();
});