<% if $VisibleCategories %>
  <div class="categories">
    <% loop $VisibleCategories %>
      <article class="category">
        <header>
          <h3>$Title</h3>
        </header>
        <% if $Category.ContentShown %>
          <div class="content">
            $Category.Content
          </div>
        <% end_if %>
        <div class="members">
          $Members
        </div>
      </article>
    <% end_loop %>
  </div>
<% else %>
  <% include Alert Type='warning', Text=$NoDataMessage %>
<% end_if %>
