import { Outlet } from 'react-router-dom'
import { ContentModelMarkupProvider } from '../context/ContentModelMarkupContext'

export const ContentModelMarkupLayout = () => {
    return (
        <ContentModelMarkupProvider>
            <Outlet />
        </ContentModelMarkupProvider>
    )
}
