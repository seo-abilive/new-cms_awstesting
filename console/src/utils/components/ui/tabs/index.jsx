import { Tabs as FTabs } from 'flowbite-react'
import { TabItem as FTabItem } from 'flowbite-react'

export const Tabs = ({ ariaLabel = 'default', variant = 'default', children, ...props }) => {
    return (
        <FTabs aria-label={ariaLabel} variant={variant} {...props}>
            {children}
        </FTabs>
    )
}

export const TabItem = ({ title = '', children, ...props }) => {
    return (
        <FTabItem title={title} {...props}>
            {children}
        </FTabItem>
    )
}
