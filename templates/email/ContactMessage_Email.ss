<!DOCTYPE html>

<html>
  <head>
    <% base_tag %>
    <meta charset="utf-8">
    <title>Contact Message</title>
  </head>
  <body>
    <div class="message">
      <p><% _t('ContactMessage_Email_ss.HI', 'Hi') %> {$Recipient.Name},</p>
      <p><% _t('ContactMessage_Email_ss.INTRO', 'You have received a message from:') %></p>
      <% with $Message %>
        <dl>
          <dt><% _t('ContactMessage_Email_ss.NAME', 'Name') %></dt>
          <dd>{$FullName}</dd>
          <dt><% _t('ContactMessage_Email_ss.EMAIL', 'Email') %></dt>
          <dd><a href="mailto:{$Message.Email}">{$Email}</a></dd>
          <% if $Phone %>
            <dt><% _t('ContactMessage_Email_ss.PHONE', 'Phone') %></dt>
            <dd>{$Phone}</dd>
          <% end_if %>
          <% if $Subject %>
            <dt><% _t('ContactMessage_Email_ss.SUBJECT', 'Subject') %></dt>
            <dd>{$Subject}</dd>
          <% end_if %>
        </dl>
        <p><% _t('ContactMessage_Email_ss.MESSAGEFOLLOWS', 'The message follows:') %></p>
        <p>{$Message}</p>
      <% end_with %>
    </div>
  </body>
</html>