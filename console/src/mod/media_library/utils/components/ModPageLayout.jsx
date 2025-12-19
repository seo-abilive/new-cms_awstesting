import { Outlet } from 'react-router-dom'
import { ModPageProvider } from '../context/ModPageContext'

export const ModPageLayout = () => {
    return (
        <ModPageProvider>
            <Outlet />
        </ModPageProvider>
    )
}
