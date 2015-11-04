/*! Smooth Scroll - v1.4.7 - 2012-10-29
* Copyright (c) 2012 Karl Swedberg; Licensed MIT, GPL */
(function(a){function f(a){return a.replace(/(:|\.)/g,"\\$1")}var b="1.4.7",c={exclude:[],excludeWithin:[],offset:0,direction:"top",scrollElement:null,scrollTarget:null,beforeScroll:function(){},afterScroll:function(){},easing:"swing",speed:400,autoCoefficent:2},d=function(b){var c=[],d=!1,e=b.dir&&b.dir=="left"?"scrollLeft":"scrollTop";return this.each(function(){if(this==document||this==window)return;var b=a(this);b[e]()>0?c.push(this):(b[e](1),d=b[e]()>0,d&&c.push(this),b[e](0))}),c.length||this.each(function(a){this.nodeName==="BODY"&&(c=[this])}),b.el==="first"&&c.length>1&&(c=[c[0]]),c},e="ontouchend"in document;a.fn.extend({scrollable:function(a){var b=d.call(this,{dir:a});return this.pushStack(b)},firstScrollable:function(a){var b=d.call(this,{el:"first",dir:a});return this.pushStack(b)},smoothScroll:function(b){b=b||{};var c=a.extend({},a.fn.smoothScroll.defaults,b),d=a.smoothScroll.filterPath(location.pathname);return this.unbind("click.smoothscroll").bind("click.smoothscroll",function(b){var e=this,g=a(this),h=c.exclude,i=c.excludeWithin,j=0,k=0,l=!0,m={},n=location.hostname===e.hostname||!e.hostname,o=c.scrollTarget||(a.smoothScroll.filterPath(e.pathname)||d)===d,p=f(e.hash);if(!c.scrollTarget&&(!n||!o||!p))l=!1;else{while(l&&j<h.length)g.is(f(h[j++]))&&(l=!1);while(l&&k<i.length)g.closest(i[k++]).length&&(l=!1)}l&&(b.preventDefault(),a.extend(m,c,{scrollTarget:c.scrollTarget||p,link:e}),a.smoothScroll(m))}),this}}),a.smoothScroll=function(b,c){var d,e,f,g,h=0,i="offset",j="scrollTop",k={},l={},m=[];typeof b=="number"?(d=a.fn.smoothScroll.defaults,f=b):(d=a.extend({},a.fn.smoothScroll.defaults,b||{}),d.scrollElement&&(i="position",d.scrollElement.css("position")=="static"&&d.scrollElement.css("position","relative"))),d=a.extend({link:null},d),j=d.direction=="left"?"scrollLeft":j,d.scrollElement?(e=d.scrollElement,h=e[j]()):e=a("html, body").firstScrollable(),d.beforeScroll.call(e,d),f=typeof b=="number"?b:c||a(d.scrollTarget)[i]()&&a(d.scrollTarget)[i]()[d.direction]||0,k[j]=f+h+d.offset,g=d.speed,g==="auto"&&(g=k[j]||e.scrollTop(),g=g/d.autoCoefficent),l={duration:g,easing:d.easing,complete:function(){d.afterScroll.call(d.link,d)}},d.step&&(l.step=d.step),e.length?e.stop().animate(k,l):d.afterScroll.call(d.link,d)},a.smoothScroll.version=b,a.smoothScroll.filterPath=function(a){return a.replace(/^\//,"").replace(/(index|default).[a-zA-Z]{3,4}$/,"").replace(/\/$/,"")},a.fn.smoothScroll.defaults=c})(jQuery);;/*!
 * jquery.customSelect() - v0.5.1
 * http://adam.co/lab/jquery/customselect/
 * 2014-04-19
 *
 * Copyright 2013 Adam Coulombe
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @license http://www.gnu.org/licenses/gpl.html GPL2 License 
 */
(function(a){a.fn.extend({customSelect:function(c){if(typeof document.body.style.maxHeight==="undefined"){return this}var e={customClass:"customSelect",mapClass:true,mapStyle:true},c=a.extend(e,c),d=c.customClass,f=function(h,k){var g=h.find(":selected"),j=k.children(":first"),i=g.html()||"&nbsp;";j.html(i);if(g.attr("disabled")){k.addClass(b("DisabledOption"))}else{k.removeClass(b("DisabledOption"))}setTimeout(function(){k.removeClass(b("Open"));a(document).off("mouseup.customSelect")},60)},b=function(g){return d+g};return this.each(function(){var g=a(this),i=a("<span />").addClass(b("Inner")),h=a("<span />");g.after(h.append(i));h.addClass(d);if(c.mapClass){h.addClass(g.attr("class"))}if(c.mapStyle){h.attr("style",g.attr("style"))}g.addClass("hasCustomSelect").on("render.customSelect",function(){f(g,h);g.css("width","");var k=parseInt(g.outerWidth(),10)-(parseInt(h.outerWidth(),10)-parseInt(h.width(),10));h.css({display:"inline-block"});var j=h.outerHeight();if(g.attr("disabled")){h.addClass(b("Disabled"))}else{h.removeClass(b("Disabled"))}i.css({width:k,display:"inline-block"});g.css({"-webkit-appearance":"menulist-button",width:h.outerWidth(),position:"absolute",opacity:0,height:j,fontSize:h.css("font-size")})}).on("change.customSelect",function(){h.addClass(b("Changed"));f(g,h)}).on("keyup.customSelect",function(j){if(!h.hasClass(b("Open"))){g.trigger("blur.customSelect");g.trigger("focus.customSelect")}else{if(j.which==13||j.which==27){f(g,h)}}}).on("mousedown.customSelect",function(){h.removeClass(b("Changed"))}).on("mouseup.customSelect",function(j){if(!h.hasClass(b("Open"))){if(a("."+b("Open")).not(h).length>0&&typeof InstallTrigger!=="undefined"){g.trigger("focus.customSelect")}else{h.addClass(b("Open"));j.stopPropagation();a(document).one("mouseup.customSelect",function(k){if(k.target!=g.get(0)&&a.inArray(k.target,g.find("*").get())<0){g.trigger("blur.customSelect")}else{f(g,h)}})}}}).on("focus.customSelect",function(){h.removeClass(b("Changed")).addClass(b("Focus"))}).on("blur.customSelect",function(){h.removeClass(b("Focus")+" "+b("Open"))}).on("mouseenter.customSelect",function(){h.addClass(b("Hover"))}).on("mouseleave.customSelect",function(){h.removeClass(b("Hover"))}).trigger("render.customSelect")})}})})(jQuery);;/**
 * OpenLab search dropdowns
 */

(function ($) {

    if (window.OpenLab === undefined) {
        var OpenLab = {};
    }

    var legacyWidth = $(window).width();
    var resizeTimer;

    OpenLab.search = {
        init: function () {

            //search
            if ($('.search-trigger-wrapper').length) {
                OpenLab.search.searchBarLoadActions();
                $('.search-trigger').on('click', function () {
                    OpenLab.search.searchBarEventActions($(this));
                });
            }

        },
        searchBarLoadActions: function () {

            $('.search-form-wrapper').each(function () {
                var searchFormDim = OpenLab.search.invisibleDimensions($(this));
                $(this).data('thisheight', searchFormDim.height);
            });

        },
        searchBarEventActions: function (searchTrigger) {

            var select = $('.search-form-wrapper .hidden-custom-select select');
            var adminBar = $('#wpadminbar');
            var mode = searchTrigger.data('mode');
            var location = searchTrigger.data('location');
            var searchForm = $('.search-form-wrapper.search-mode-' + mode + '.search-form-location-' + location);
            if (!searchTrigger.hasClass('in-action')) {
                searchTrigger.addClass('in-action');
                if (searchTrigger.parent().hasClass('search-live')) {
                    searchTrigger.parent().toggleClass('search-live');
                    if (searchTrigger.data('mode') == 'mobile' && searchTrigger.data('location') == 'header') {
                        adminBar.animate({
                            top: "-=" + searchForm.data('thisheight')
                        }, 700);
                        adminBar.removeClass('dropped');
                    }

                    searchForm.slideUp(800, function () {
                        searchTrigger.removeClass('in-action');
                    });


                } else {
                    searchTrigger.parent().toggleClass('search-live');
                    if (searchTrigger.data('mode') == 'mobile' && searchTrigger.data('location') == 'header') {
                        adminBar.addClass('dropped');
                        adminBar.animate({
                            top: "+=" + searchForm.data('thisheight')
                        }, 700);
                    }
                    searchForm.slideDown(700, function () {
                        searchTrigger.removeClass('in-action');
                    });
                }
                select.customSelect();
            }

        },
        invisibleDimensions: function (el) {

            $(el).css({
                'display': 'block',
                'visibility': 'hidden'
            });
            var dim = {
                height: $(el).outerHeight(),
                width: $(el).outerWidth()
            };
            $(el).css({
                'display': 'none',
                'visibility': ''
            });
            return dim;
        },
        isBreakpoint: function (alias) {
            return $('.device-' + alias).is(':visible');
        },
    }

    $(document).ready(function () {

        OpenLab.search.init();

    });

    $(window).on('resize', function (e) {

        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {

            if ($(this).width() != legacyWidth) {
                legacyWidth = $(this).width();
                if ($('.search-trigger-wrapper.search-live').length) {
                    OpenLab.search.searchBarEventActions($('.search-trigger-wrapper.search-live').find('.search-trigger'));
                }
            }

        }, 250);

    });

})(jQuery);;/**
 * OpenLab search dropdowns
 */

(function ($) {

    if (window.OpenLab === undefined) {
        var OpenLab = {};
    }

    var resizeTimer;

    OpenLab.nav = {
        backgroundCont: {},
        plusHeight: 66,
        init: function () {

            OpenLab.nav.backgroundCont = $('#behind_menu_background');

            OpenLab.nav.directToggleAction();
            OpenLab.nav.backgroundAction();
            OpenLab.nav.mobileAnchorLinks();
            OpenLab.nav.hoverFixes();
            
            OpenLab.nav.hyphenateInit();

        },
        hyphenateInit: function () {
            Hyphenator.config(
                    {onhyphenationdonecallback: onHyphenationDone = function (context) {
                            return undefined;
                        },
                        useCSS3hyphenation: true
                    }
            );
            Hyphenator.run();
        },
        hoverFixes: function () {
            //fixing hover issues on mobile
            if (OpenLab.nav.isBreakpoint('xs') || OpenLab.nav.isBreakpoint('sm')) {
                $('.mobile-no-hover').bind('touchend', function () {
                    OpenLab.nav.fixHoverOnMobile($(this));
                })
            }
        },
        directToggleAction: function () {

            //if there is no direct toggle, we're done
            if (!$('.direct-toggle').length) {
                return false;
            }

            var directToggle = $('.direct-toggle');

            directToggle.on('click', function (e) {
                directToggle.removeClass('active')
                e.stopImmediatePropagation();

                var thisElem = $(this);

                thisElem.addClass('active');
                if (!thisElem.hasClass('in-action')) {

                    directToggle.removeClass('in-action');
                    thisElem.addClass('in-action');

                    var thisTarget = $(this).data('target');
                    var thisTargetElem = $(thisTarget);

                    if (thisTargetElem.is(':visible')) {

                        OpenLab.nav.hideNavMenu(thisElem, thisTargetElem);

                    } else {

                        directToggle.each(function () {
                            var thisElem = $(this);
                            var thisToggleTarget = thisElem.data('target');

                            if ($(thisToggleTarget).is(':visible')) {

                                OpenLab.nav.hideNavMenu(thisElem, thisToggleTarget);

                            }
                        });

                        OpenLab.nav.showNavMenu(thisElem, thisTargetElem);

                    }
                }
            });
        },
        hideNavMenu: function (thisElem, thisToggleTarget, thisAnchor) {
            var plusHeight = OpenLab.nav.plusHeight;

            if (thisElem.attr('data-plusheight')) {
                plusHeight = parseInt(thisElem.data('plusheight'));
            }

            var thisTargetElem_h = $(thisToggleTarget).height();
            thisTargetElem_h += plusHeight;

            OpenLab.nav.backgroundCont.removeClass('active').animate({
                'opacity': 0,
                'top': '-=' + thisTargetElem_h + 'px'
            }, 50, function () {
                $(this).hide();
            });
            $(thisToggleTarget).slideUp(700, function () {
                thisElem.removeClass('in-action');
                thisElem.removeClass('active');

                if (thisAnchor) {
                    $.smoothScroll({
                        scrollTarget: thisAnchor
                    });
                }

            });
        },
        showNavMenu: function (thisElem, thisTargetElem) {
            var plusHeight = OpenLab.nav.plusHeight;

            if (thisElem.attr('data-plusheight')) {
                plusHeight = parseInt(thisElem.data('plusheight'));
            }

            thisTargetElem.slideDown(700, function () {

                var thisTargetElem_h = thisTargetElem.height();
                thisTargetElem_h += plusHeight;

                thisElem.removeClass('in-action');

                OpenLab.nav.backgroundCont.addClass('active').show()
                        .css({
                            'top': '+=' + thisTargetElem_h + 'px'
                        })
                        .animate({
                            'opacity': 0.42,
                        }, 500);

                //for customSelect
                $('.custom-select').each(function () {
                    var customSelect_h = $(this).find('.customSelect').outerHeight();
                    var customSelect_w = $(this).find('.customSelect').outerWidth();
                    $(this).find('select').css({
                        'height': customSelect_h + 'px',
                        'width': customSelect_w + 'px'
                    });
                })
            });
        },
        backgroundAction: function () {

            OpenLab.nav.backgroundCont.on('click', function () {

                var thisElem = $(this);
                var currentActiveButton = $('.direct-toggle.active');
                var targetToClose = currentActiveButton.data('target');

                OpenLab.nav.hideNavMenu(currentActiveButton, targetToClose);

            });

        },
        mobileAnchorLinks: function () {
            if ($('.mobile-anchor-link').length) {
                $('.mobile-anchor-link').find('a').on('click', function (e) {
                    e.preventDefault();
                    var thisElem = $(this);
                    var thisAnchor = thisElem.attr('href');

                    var currentActiveButton = $('.direct-toggle.active');
                    var background = $('#behind_menu_background');
                    var targetToClose = currentActiveButton.data('target');

                    OpenLab.nav.hideNavMenu(currentActiveButton, targetToClose, thisAnchor);

                });
            }
        },
        isBreakpoint: function (alias) {
            return $('.device-' + alias).is(':visible');
        },
        fixHoverOnMobile: function (thisElem) {
            thisElem.trigger('click');
        }
    };

    $(document).ready(function () {

        OpenLab.nav.init();

    });

    $(window).on('resize', function (e) {

        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            
            OpenLab.nav.hoverFixes();

        }, 250);

    });

})(jQuery);;/**
 * Any client-side theme fixes go here (for group site themes; excludes OpenLab custom theme)
 */

/**
 * Twentyfourteen
 * Makes the header relative until scrolling, to fix issue with header going behind admin bar
 */

(function ($) {

    if (window.OpenLab === undefined) {
        var OpenLab = {};
    }

    OpenLab.fixes = {
        init: function () {

            if ($('body').hasClass('masthead-fixed')) {
                OpenLab.fixes.fixMasthead();
            }

        },
        fixMasthead: function () {

            //this is so that the on scroll function won't fire on themes that don't need it to
            if (!$('body').hasClass('masthead-fixing')) {
                $('body').addClass('masthead-fixing');
            }

            //get adminbar height
            var adminBar_h = $('#wpadminbar').outerHeight();
            var scrollTrigger = Math.ceil(adminBar_h / 2);
            
            //if were below the scrollTrigger, remove the fixed class, otherwise make sure it's there
            if(OpenLab.fixes.getCurrentScroll() <= scrollTrigger){
                $('body').removeClass('masthead-fixed');
            } else {
                $('body').addClass('masthead-fixed');
            }

        },
        getCurrentScroll: function () {
            var currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            
            return currentScroll;
        }
    };

    $(document).ready(function () {
        OpenLab.fixes.init();
    });

    $(window).scroll(function () {

        if ($('body').hasClass('masthead-fixing')) {
            OpenLab.fixes.fixMasthead();
        }
    });

})(jQuery);