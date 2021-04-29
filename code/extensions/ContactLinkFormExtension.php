<?php

/**
 * An extension of the link form extension class to add contact fields to the editor field link form.
 */
class ContactLinkFormExtension extends SilverWareLinkFormExtension
{
    /**
     * @config
     * @var string
     */
    private static $link_type_value = "contact";
    
    /**
     * @config
     * @var string|array
     */
    private static $link_type_label = array(
        'ContactLinkFormExtension.LINKCONTACT',
        'Contact page'
    );
    
    /**
     * @config
     * @var string
     */
    private static $link_shortcode = "contact_link";
    
    /**
     * Answers the source for the default link field.
     *
     * @return array|ArrayAccess
     */
    protected function getLinkFieldSource()
    {
        // Obtain Source Array (from parent):
        
        $source = parent::getLinkFieldSource();
        
        // Define Source Array:
        
        foreach (ContactPage::get() as $Page) {
            
            // Create Contact Page Option:
            
            $source[$Page->ID] = $Page->MenuTitle;
            
            // Create Contact Recipient Options (if enabled):
            
            if ($Page->ShowRecipientField) {
                
                foreach ($Page->getEnabledRecipients() as $Recipient) {
                    
                    $source["{$Page->ID}-{$Recipient->ID}"] = sprintf(
                        '%s - %s',
                        $Page->MenuTitle,
                        $Recipient->NameAndSendTo
                    );
                    
                }
                
            }
            
        }
        
        // Answer Source Array:
        
        return $source;
    }
}
