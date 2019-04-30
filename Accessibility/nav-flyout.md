 ### Accessible Flyout Navigation on Desktop and Mobile
_Example is in Drupal 7/8, but approach is tech agnostic_

Based on [W3's best practices](https://www.w3.org/WAI/tutorials/menus/flyout/#flyoutnavkbbtn
) for accessible navs where the top level items are links, but also have flyouts/dropdowns.

###### Summary: the JS function drops this markup with the correct label after each menu item that has flyouts/dropdowns. It targets the 'expanded' class, so  update accordingly. Theme the .arrow to match the design. In this example, it's a chevron pointing down.

```
<button aria-expanded="false">
  <span class="arrow">
    <span class="visually-hidden">[the name of this menu item]'s submenu</span>
  </span>
</button>
```

###### Then, the JS adds an 'open' class to the top menu item when the button is clicked (with a keyboard or mouse). The theming then keeps the dropdown appearing as long as the 'open' class is present.

_See in SPLC's main navigation on desktop and mobile for live sample._

** JS Functions: **

```
  // Adds aria labels and dropdown chevron buttons to menu items with flyouts (li.expanded button)
  accessibleNav: function() {
    var axsMenuItem = $('.menu-name-main-menu li.expanded');
    $('.menu-name-main-menu li.expanded button').attr('aria-expanded','false');

    // Adds the button and label after each flyout menu item  
    axsMenuItem.each(function() {
      var activatingA = $(this).children('a');
      var btn = '<button><span class="arrow"><span class="visually-hidden">show submenu for “' + activatingA.text() + '”</span></span></button>';
      activatingA.after(btn);

      // Triggers flyouts on mouse click or return button press
      $(this).find('button').on('click', function(event){
        event.preventDefault();
        var li = $(this).parents('li.expanded').toggleClass('open');
        $(this).attr('aria-expanded', li.hasClass('open') ? 'true' : 'false');
      });
    });

    // Closes flyout menu items when you tab to the next top level menu item
    $('.menu-block-wrapper > ul.menu > li > a').on('focus', function(){
      axsMenuItem.removeClass('open').find('button').attr('aria-expanded', 'false');
    });
  },

 this.toggleNav();
 this.accessibleNav();

```

Theming will be specific per project, but this should help give you an idea of how the focus and hover states can be addressed. This would be cleaner if the `<a>` tags were targeted instead of the `<li>`, then the focus and hover would be on the same element.

** SASS: **
```
// Focus/hover state for top level menu items
%menu-theming {
  background: $background-color;
  color: $white;
  @include transition(all 0.1s);
}

// Themes button that triggers dropdown. In this case it's a chevron
%menu-theming-chevron {
  @extend %splc-chevron-up;
  color: $white;
  font-weight: bold;
  font-size: em(12px);
  margin-left: 0.5em;
}

// Displays flyout/dropdown menu
%show-me-the-menu {
  // Show the second level
  visibility: visible;
  opacity: 1;
  @include transition(0.2s opacity);

}


// Visually hides an element, while keeping it accessible to screen readers. From W3 best practices. THIS IS BUILT INTO D8. You can just use the class without defining it.
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

//Puts the button/chevron inside the nav item, makes space for the chevron in the <a> tag so the hover looks correct
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded > a {
  margin-right: -1.5rem;
  padding-right: 2rem;
  z-index: 1;
}

//Adds top level menu theming when menu item flyout is open
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded.open > a,
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded:hover > a {
  @extend %menu-theming;
}

// Displays submenu when hovering or tabbing to the button and hitting return
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded:hover > ul.menu,
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded.open ul.menu {
  @extend %show-me-the-menu;
}

//Makes chevron white on focus or hover
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded:hover,
#main-navigation #block-menu_block-1 .menu-block-wrapper > ul.menu > li.expanded.open {
  span.arrow:before { color: $white; }
}


// Makes submenu visible
#main-navigation #block-menu_block-1  .menu-block-wrapper > ul.menu > li.open > ul.menu {
  display: block;
  opacity 1;
}

```

### Here are more generic styles that might be helpful, but key element targeting is done above.


** JS **
```
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
```

** SASS **
```
// This span is added in the JS. Sets base styles
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

// Second level list items
#main-navigation #block-menu_block-1  .menu-block-wrapper > ul.menu > li > ul.menu {
  background: $brick-red;
  opacity: 0;
  padding-bottom: 1em;
  position: absolute;
  top: 2.4em;
  left: 1px;
  width: 140%;
  visibility: hidden;
  z-index: 10;

  @include mq(t) {
    background: $dune;
    display: none;
    position: relative;
    opacity: 1;
    left: 0;
    top: 0;
    width: auto;
    visibility: visible;
  }

  @include mq(m) {
    background: $background-color;
    display: none;
    left: 0;
    opacity: 1;
    position: relative;
    top: 0;
    width: auto;
    visibility: visible;
  }

  & li {
    display: list-item;
    list-style: none;
  }

  & li a {
    color: $white;
    display: block;
    font-family: $heading-font;
    padding: 0.5em;
  }

  & li a:hover,
  & li a:focus {
    color: $hocus-color;

    @include mq(t) {
      color: $white;
    }

    @include mq(m) {
      color: $white;
    }
  }
}
```
