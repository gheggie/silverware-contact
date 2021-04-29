<% if $EnabledItems %>
  <div class="$ItemWrapperClass" data-squery="min-width:{$RowsWidth}px=wide">
    <% loop $EnabledItems %>
      $Me
    <% end_loop %>
  </div>
<% end_if %>