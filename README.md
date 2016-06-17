# Magento 1.x - Filter Payment Methods on Attribute Set

Adds the ability to filter core payment methods based on attribute set in Magento 1.x

Using a none core payment method? 
Not a problem, just create a module and add the filter fields to your system.xml file as shown below.

```
<?xml version="1.0" encoding="UTF-8"?>
<config>
    <sections>
        <payment>
            <groups>
                <authorizenet>
                    <fields>
                        <attribute_sets_filter translate="label">
                            <label>Limit by Attribute Set</label>
                            <frontend_type>select</frontend_type>
                            <source_model>adminhtml/system_config_source_yesno</source_model>
                            <sort_order>54</sort_order>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>1</show_in_store>
                        </attribute_sets_filter>
                        <attribute_sets_to_filter translate="label">
                            <label>Restricted Attribute Sets</label>
                            <frontend_type>multiselect</frontend_type>
                            <sort_order>55</sort_order>
                            <source_model>peacockcarter_adminhtml/system_config_source_attributeset</source_model>
                            <show_in_default>1</show_in_default>
                            <show_in_website>1</show_in_website>
                            <show_in_store>0</show_in_store>
                            <can_be_empty>1</can_be_empty>
                            <depends>
                                <attribute_sets_filter>1</attribute_sets_filter>
                            </depends>
                        </attribute_sets_to_filter>
                    </fields>
                </authorizenet>
            </groups>
        </payment>
    </sections>
</config>

```

Ensure that the group is named the same as the payment method code e.g. authorizenet.

Don't change the field names (i.e. attribute_sets_filter and attribute_sets_to_filter), the observer uses these to get your settings from the database.

Tested on Magento CE1.9 and EE1.1.4