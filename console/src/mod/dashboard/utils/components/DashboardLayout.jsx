import { Outlet } from 'react-router-dom'
import { DashboardProvider } from '../context/DashboardContext'

export const DashboardLayout = () => {
    return (
        <DashboardProvider>
            <Outlet />
        </DashboardProvider>
    )
}
