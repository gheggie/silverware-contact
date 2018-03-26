<% if $ShowTitle %>
  <header>
    <$HeadingTag>
      <% if $ShowIcon %>{$FontIconTag}<% end_if %>
      <% if $ShowText %><span class="text">{$Title}</span><% end_if %>
    </$HeadingTag>
  </header>
<% end_if %>
