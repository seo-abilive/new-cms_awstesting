import { Outlet } from 'react-router-dom'
import { ModPageProvider } from '@/mod/contact_setting/utils/context/ModPageContext'

export const ModPageLayout = () => {
    return (
        <ModPageProvider>
            <Outlet />
        </ModPageProvider>
    )
}
