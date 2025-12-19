import { routes as skeletonRoutes } from '../mod/_skeleton/routes/routes'
import {
    routes as masterDashboardRoutes,
    manageRoutes as manageDashboardRoutes,
} from '../mod/dashboard/routes/routes'
import { routes as contentModelRoutes } from '../mod/content_model/routes/routes'
import { routes as actionLogRoutes } from '../mod/action_log/routes/routes'
import { routes as contentFieldRoutes } from '../mod/content_field/routes/routes'
import { routes as contentRoutes } from '../mod/content/routes/routes'
import { routes as mediaLibraryRoutes } from '../mod/media_library/routes/routes'
import { routes as contactSettingRoutes } from '../mod/contact_setting/routes/routes'
import { routes as userRoutes } from '../core/user/routes/routes'
import { routes as contractRoutes } from '../core/contract/routes/routes'
import { routes as companyUserRoutes } from '../mod/company_user/routes/routes'

export const masterRoutes = [
    ...skeletonRoutes,
    ...masterDashboardRoutes,
    ...contentModelRoutes,
    ...actionLogRoutes,
    ...contentFieldRoutes,
    ...userRoutes,
    ...contractRoutes,
]

export const manageRoutes = [
    ...manageDashboardRoutes,
    ...contactSettingRoutes,
    ...mediaLibraryRoutes,
    ...companyUserRoutes,
    ...contentRoutes,
]
