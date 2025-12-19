import { BrowserRouter as Router, Routes, Route } from 'react-router'
import { AppLayout } from './utils/components/layout/AppLayout'
import { ScrollToTop } from './utils/components/common/ScrollToTop'
import { NotFound } from './utils/pages/OtherPage/NotFound'
import { masterRoutes as masterMenuRoutes, manageRoutes as manageMenuRoutes } from './routes/routes'
import { Toaster } from 'sonner' // Toasterをインポート
import config from './config/configLoader'
import { routes as widgetRoutes } from './mod/widget/routes/routes'
import { LoginLayout } from './utils/components/layout/LoginLayout'
import { Login } from './core/user/page/Login'
import { ResetRequest } from './core/user/page/ResetRequest'
import { ResetForm } from './core/user/page/ResetForm'
import { Account } from './core/user/page/Account'
import { AuthProvider } from '@/utils/context/AuthContext'
import { RoleGuard } from '@/utils/components/routing/RoleGuard'
import { RootRedirect } from '@/utils/components/routing/RootRedirect'

function App() {
    return (
        <>
            <AuthProvider>
                <Router basename={config.basename}>
                    <ScrollToTop />
                    <Routes>
                        {/* ルートパス: 認証後にダッシュボードへリダイレクト */}
                        <Route path="/" element={<RootRedirect />} />
                        <Route element={<LoginLayout />}>
                            <Route path="/login" element={<Login />} />
                            <Route path="/reset-password" element={<ResetRequest />} />
                            <Route path="/reset-password/confirm" element={<ResetForm />} />
                        </Route>
                        <Route
                            element={
                                <RoleGuard>
                                    <AppLayout adminPanel="master" />
                                </RoleGuard>
                            }
                        >
                            {masterMenuRoutes.map((route, idx) => {
                                if (route.children) {
                                    return (
                                        <Route element={<route.element />} key={idx}>
                                            {route.children.map((childRoute, childIdx) => {
                                                return (
                                                    <Route
                                                        path={childRoute.path}
                                                        element={<childRoute.element />}
                                                        key={childIdx}
                                                    />
                                                )
                                            })}
                                            <Route path="/account" element={<Account />} />
                                        </Route>
                                    )
                                }
                                return (
                                    <Route
                                        path={route.path}
                                        element={<route.element />}
                                        key={idx}
                                    />
                                )
                            })}
                        </Route>
                        <Route
                            element={
                                <RoleGuard>
                                    <AppLayout adminPanel="manage" />
                                </RoleGuard>
                            }
                        >
                            {manageMenuRoutes.map((route, idx) => {
                                if (route.children) {
                                    return (
                                        <Route element={<route.element />} key={idx}>
                                            {route.children.map((childRoute, childIdx) => {
                                                return (
                                                    <Route
                                                        path={childRoute.path}
                                                        element={<childRoute.element />}
                                                        key={childIdx}
                                                    />
                                                )
                                            })}
                                        </Route>
                                    )
                                }
                                return (
                                    <Route
                                        path={route.path}
                                        element={<route.element />}
                                        key={idx}
                                    />
                                )
                            })}
                        </Route>
                        {/* widgetルートをAppLayoutの外に配置 */}
                        {widgetRoutes.map((route, idx) => {
                            if (route.children) {
                                return (
                                    <Route element={<route.element />} key={idx}>
                                        {route.children.map((childRoute, childIdx) => {
                                            return (
                                                <Route
                                                    path={childRoute.path}
                                                    element={<childRoute.element />}
                                                    key={childIdx}
                                                />
                                            )
                                        })}
                                    </Route>
                                )
                            }
                            return <Route path={route.path} element={<route.element />} key={idx} />
                        })}
                        <Route path="*" element={<NotFound />} />
                    </Routes>
                </Router>
            </AuthProvider>
            <Toaster position="top-right" richColors /> {/* Toasterを追加 */}
        </>
    )
}

export default App
