import { Outlet } from 'react-router-dom'
import { ContentFieldProvider } from '@/mod/content_field/utils/context/ContentFieldContext'

export const ContentFieldLayout = () => {
    return (
        <ContentFieldProvider>
            <Outlet />
        </ContentFieldProvider>
    )
}
