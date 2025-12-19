import { Outlet } from 'react-router-dom'
import { ModPageProvider } from '@/mod/action_log/utils/context/ModPageContext'

export const ModPageLayout = () => {
    return (
        <ModPageProvider>
            <Outlet />
        </ModPageProvider>
    )
}
