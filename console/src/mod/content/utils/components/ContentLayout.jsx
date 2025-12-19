import { Outlet } from 'react-router-dom'
import { ContentContextProvider } from '@/mod/content/utils/context/ContentContext'

export const ContentLayout = () => {
    return (
        <ContentContextProvider>
            <Outlet />
        </ContentContextProvider>
    )
}
