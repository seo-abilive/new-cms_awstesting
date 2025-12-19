import { SidebarProvider, useSidebar } from '@/utils/context/SidebarContext'
import { Outlet, Navigate } from 'react-router'
import { AppHeader } from '@/utils/components/layout/AppHeader'
import { Backdrop } from '@/utils/components/layout/Backdrop'
import { AppSidebar } from '@/utils/components/layout/AppSidebar'
import { useAuth } from '@/utils/context/AuthContext'
import { CompanyFacilityProvider } from '@/utils/context/CompanyFacilityContext'

const LayoutContent = () => {
    const { isExpanded, isHovered, isMobileOpen } = useSidebar()
    const { user, loading } = useAuth()

    return (
        <div className="min-h-screen xl:flex">
            {loading && (
                <div className="flex items-center justify-center w-full h-screen">
                    <div className="text-gray-500">Loading...</div>
                </div>
            )}
            {!loading && !user && <Navigate to="/login" replace />}
            {!loading && user && (
                <div>
                    <AppSidebar />
                    <Backdrop />
                </div>
            )}
            {!loading && user && (
                <div
                    className={`flex-1 transition-all duration-300 ease-in-out ${
                        isExpanded || isHovered ? 'lg:ml-[250px]' : 'lg:ml-[70px]'
                    } ${isMobileOpen ? 'ml-0' : ''}`}
                >
                    <AppHeader />
                    <div className="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                        <Outlet />
                    </div>
                </div>
            )}
        </div>
    )
}

export const AppLayout = ({ adminPanel }) => {
    const content = <LayoutContent />
    return (
        <SidebarProvider adminPanel={adminPanel}>
            <CompanyFacilityProvider adminPanel={adminPanel}>{content}</CompanyFacilityProvider>
        </SidebarProvider>
    )
}
