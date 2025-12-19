import { Outlet } from 'react-router-dom'
import { ContentModelProvider } from '../context/ContentModelContext'

export const ContentModelLayout = () => {
    return (
        <ContentModelProvider>
            <Outlet />
        </ContentModelProvider>
    )
}
