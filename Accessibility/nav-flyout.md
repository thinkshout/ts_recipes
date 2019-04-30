Based on W3's best practices for accessible navs where the top level items are links, but also have flyout dropdowns. 
https://www.w3.org/WAI/tutorials/menus/flyout/#flyoutnavkbbtn
See in SPLC's main navigation on desktop and mobile. 


** JS Functions: **

  // Toggles the mobile nav open and closed with click or keyboard:
  toggleNav: function() {
    $('.nav-toggle').click(function() {
      $(this).toggleClass('open');
      event.preventDefault();
      $('#main-navigation, #secondary-nav').slideToggle(200);
    });

    $('#main-navigation #block-menu_block-1 li.expanded button').on("click", function(event) {
      $(this).parent('li.expanded').find('ul.menu').slideToggle();
    });
  },

  // Adds aria labels and dropdown chevron buttons to menu items with flyouts (li.expanded)
  accessibleNav: function() {
    var axsMenuItem = $('.menu-name-main-menu li.expanded');
    axsMenuItem.attr({
      'aria-haspopup':'true',
      'aria-expanded':'false'
    });

    // Adds the button and label after each flyout menu item  
    axsMenuItem.each(function() {
      var activatingA = $(this).children('a');
      var btn = '<button><span class="arrow"><span class="visually-hidden">show submenu for “' + activatingA.text() + '”</span></span></button>';
      activatingA.after(btn);

      // Triggers flyouts on mouse click or return button press
      $(this).find('button').on("click",  function(event){
        event.preventDefault();
        var a = $(this);
        if (a.parents('li.expanded')) {
          a.parents('li.expanded').toggleClass('open');
          a.parents('a').attr('aria-expanded', "true");
          a.parents('button').attr('aria-expanded', "true");
        } else {
          a.parents('li').removeClass('open');
          a.parents('a').attr('aria-expanded', "false");
          a.parents('button').attr('aria-expanded', "false");
        }
      });
    });

    // Closes flyout menu items when you tab to the next top level menu item
    $('.menu-block-wrapper > ul.menu > li > a').on('focus', function(){
      axsMenuItem.removeClass('open');
    });
  },
  
 this.toggleNav(); 
 this.accessibleNav();

Theming will be specific per project, but this should help give you an idea of how the focus and hover states can be addressed. This would be cleaner if the <a> tags were targetted instead of the <li>, then the focus and hover would be on the same element. 
** SASS: **

%menu-theming {
  background: $brick-red;
  color: $white;
  @include transition(all 0.1s);
}

%menu-theming-chevron {
  @extend %splc-chevron-up;
  color: $white;
  font-weight: bold;
  font-size: em(12px);
  margin-left: 0.5em;
}

%show-me-the-menu {
  // Show the second level
  visibility: visible;
  opacity: 1;
  @include transition(0.2s opacity);

}

//From W3 best practices. THIS IS BUILT INTO D8. You can just use the class without defining it. 
.visually-hidden {
  border: 0;
  clip: rect(0 0 0 0);
  height: 1px;
  margin: -1px;
  overflow: hidden;
  padding: 0;
  position: absolute;
  width: 1px;
}

//Puts the chevron on top of the nav item, makes space for the chevron in the <a> tag so the hover looks correct
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded > a {
  margin-right: -1.5rem;
  padding-right: 2rem;
  z-index: 1;
}

//Adds red background theming when menu item flyout is open
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded.open > a,
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded:hover > a,
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded a:focus {
  @extend %menu-theming;
}

#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded:hover > ul.menu,
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded a:focus + ul.menu,
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded a.open + ul.menu,
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded.open ul.menu {
  @extend %show-me-the-menu;
}

//Makes chevron white on focus or hover
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded a:focus + button,
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded:hover,
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded.open {
  span.arrow:before { color: $white; }
}

// This span is added in the JS
span.arrow {
  color: $white;
  @include mq(md) { color: $brick-red; }
}

//Themes arrows on accessible dropdown buttons in nav
#main-navigation li.expanded {
  button {
    background-color: transparent;
    font-size: 18px;
    height: .95rem;
    line-height: 1.5;
    padding: 0;
    position: absolute;
    right: 0;
    top: 0;
    width: 1rem;
    z-index: 10;

    @media(max-width: 999px) {
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 2rem 2.41em;
      vertical-align: middle;
      width: 2rem;
    }

    @media(min-width: 1000px) {
      position: relative;
      right: inherit;
      top: inherit;
    }

    span.arrow {
      display: inline-block;
      width: inherit;
      height: inherit;
      margin-bottom: .4rem;
    }

    span.arrow:before {
      @extend %chevron-down;
      color: white;
      font-size: 1rem;
      padding: 0;
      @media(min-width: 1000px) {
        color: $button-color;
        font-size: rem(12px);
      }
    }
  }
}