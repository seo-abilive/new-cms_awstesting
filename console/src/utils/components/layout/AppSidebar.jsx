import { useCallback, useEffect, useMemo, useRef, useState } from 'react'
import { Link, useLocation } from 'react-router'

import { ChevronDownIcon, GridIcon, ListIcon, PencilIcon } from '@/utils/icons'
import { useSidebar } from '@/utils/context/SidebarContext'

import { useAxios } from '@/utils/hooks/useAxios'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'
import { useAuth } from '@/utils/context/AuthContext'
import { USER_TYPE, MASTER_FACILITY_ALIAS } from '@/core/user/utils/config'
import { sidebarConfig } from '@/mod/content/utils/config'

let navItems = []
// NOTE: navItems は従来のグローバル変数だと初期描画で反映されないため、
// コンポーネント内でもステートを持ち、描画トリガーに利用する

export const AppSidebar = () => {
    const { isExpanded, isMobileOpen, isHovered, setIsHovered, adminPanel } = useSidebar()
    const location = useLocation()

    const [openSubmenu, setOpenSubmenu] = useState(null)
    const [subMenuHeight, setSubMenuHeight] = useState({})
    const subMenuRefs = useRef({})

    const { loading, sendRequest, data } = useAxios()
    const { baseDashboardRoot, baseContentRoot, replacePath, facility_alias, company_alias } =
        useCompanyFacility()
    const { user } = useAuth()
    const [menuItems, setMenuItems] = useState([])

    // APIエンドポイントをメモ化（company_aliasとfacility_aliasが変わったときだけ再計算）
    const sidebarEndpoint = useMemo(() => {
        const endpoint = sidebarConfig.end_point
            .replace(':company_alias', company_alias || '')
            .replace(':facility_alias', facility_alias || '')
        return `${endpoint}/resource`
    }, [company_alias, facility_alias])

    // const isActive = (path: string) => location.pathname === path;
    const isActive = useCallback((path) => location.pathname === path, [location.pathname])

    useEffect(() => {
        let submenuMatched = false
        ;['admin'].forEach((menuType) => {
            const items = menuItems.length ? menuItems : navItems
            items.forEach((nav, index) => {
                if (nav.subItems) {
                    nav.subItems.forEach((subItem) => {
                        if (isActive(subItem.path)) {
                            setOpenSubmenu({
                                menuType,
                                index,
                            })
                            submenuMatched = true
                        }
                    })
                }
            })
        })

        if (!submenuMatched) {
            setOpenSubmenu(null)
        }
    }, [location, isActive, menuItems])

    useEffect(() => {
        if (openSubmenu !== null) {
            const key = `${openSubmenu.type}-${openSubmenu.index}`
            if (subMenuRefs.current[key]) {
                setSubMenuHeight((prevHeights) => ({
                    ...prevHeights,
                    [key]: subMenuRefs.current[key]?.scrollHeight || 0,
                }))
            }
        }
    }, [openSubmenu])

    // 左メニュー取得
    useEffect(() => {
        ;(async () => {
            if (adminPanel === 'master') {
                const next = [
                    {
                        icon: <GridIcon />,
                        name: 'Dashboard',
                        path: '/master/',
                    },
                    {
                        name: '契約管理',
                        icon: <ListIcon />,
                        path: '/master/contract',
                        subItems: [
                            { name: '企業', path: '/master/contract/company' },
                            {
                                name: '施設',
                                path: '/master/contract/facility',
                                state: { remove_company: true },
                            },
                        ],
                    },
                    {
                        name: 'ユーザー管理',
                        icon: <ListIcon />,
                        path: '/master/user',
                    },
                    // {
                    //     name: 'Content Model',
                    //     icon: <ListIcon />,
                    //     path: '/master/content/model',
                    // },
                    {
                        name: '操作ログ',
                        icon: <ListIcon />,
                        path: '/master/action_log',
                    },
                ]
                navItems = next
                setMenuItems(next)
            } else {
                const response = await sendRequest({
                    method: 'get',
                    url: sidebarEndpoint,
                })
                let clone = [
                    {
                        icon: <GridIcon />,
                        name: 'Dashboard',
                        path: baseDashboardRoot,
                    },
                ]
                if (response?.data) {
                    response.data.payload.data.map((menu, idx) => {
                        clone.push({
                            name: menu.title,
                            icon: <PencilIcon />,
                            path: baseContentRoot + menu.alias,
                        })
                    })
                }
                // お問い合わせ設定の権限チェック
                const contactPermissionResponse = await sendRequest({
                    method: 'get',
                    url: `user/permissions/check?resource_type=contact_setting&company_alias=${company_alias}&facility_alias=${facility_alias}`,
                })
                if (contactPermissionResponse?.data?.payload?.has_permission) {
                    clone.push({
                        name: 'お問い合わせ設定',
                        icon: <ListIcon />,
                        path: baseContentRoot + 'contact_setting',
                    })
                }
                // メディアライブラリ
                clone.push({
                    name: 'メディアライブラリ',
                    icon: <ListIcon />,
                    path: baseContentRoot + 'media_library',
                })
                // ユーザ管理（企業管理画面（facility_alias === MASTER_FACILITY_ALIAS）で、システム管理者または企業管理者の場合のみ表示）
                if (
                    facility_alias === MASTER_FACILITY_ALIAS &&
                    (user?.user_type === USER_TYPE.MASTER || user?.user_type === USER_TYPE.MANAGE)
                ) {
                    clone.push({
                        name: 'ユーザー管理',
                        icon: <ListIcon />,
                        path: baseContentRoot + 'user',
                    })
                }

                navItems = clone
                setMenuItems(clone)
            }
        })()
    }, [
        adminPanel,
        baseDashboardRoot,
        baseContentRoot,
        sidebarEndpoint,
        sendRequest,
        user?.user_type,
        facility_alias,
    ])

    const handleSubmenuToggle = (index, menuType) => {
        setOpenSubmenu((prevOpenSubmenu) => {
            if (
                prevOpenSubmenu &&
                prevOpenSubmenu.type === menuType &&
                prevOpenSubmenu.index === index
            ) {
                return null
            }
            return { type: menuType, index }
        })
    }

    const renderMenuItems = (items, menuType) => (
        <ul className="flex flex-col gap-4">
            {items.map((nav, index) => (
                <li key={index}>
                    {nav.subItems ? (
                        <button
                            onClick={() => handleSubmenuToggle(index, menuType)}
                            className={`menu-item group ${
                                openSubmenu?.type === menuType && openSubmenu?.index === index
                                    ? 'menu-item-active'
                                    : 'menu-item-inactive'
                            } cursor-pointer ${
                                !isExpanded && !isHovered ? 'lg:justify-center' : 'lg:justify-start'
                            }`}
                        >
                            <span
                                className={`menu-item-icon-size  ${
                                    openSubmenu?.type === menuType && openSubmenu?.index === index
                                        ? 'menu-item-icon-active'
                                        : 'menu-item-icon-inactive'
                                }`}
                            >
                                {nav.icon}
                            </span>
                            {(isExpanded || isHovered || isMobileOpen) && (
                                <span className="menu-item-text">{nav.name}</span>
                            )}
                            {(isExpanded || isHovered || isMobileOpen) && (
                                <ChevronDownIcon
                                    className={`ml-auto w-5 h-5 transition-transform duration-200 ${
                                        openSubmenu?.type === menuType &&
                                        openSubmenu?.index === index
                                            ? 'rotate-180 text-brand-500'
                                            : ''
                                    }`}
                                />
                            )}
                        </button>
                    ) : (
                        nav.path && (
                            <Link
                                to={nav.path}
                                className={`menu-item group ${
                                    isActive(nav.path) ? 'menu-item-active' : 'menu-item-inactive'
                                }`}
                            >
                                <span
                                    className={`menu-item-icon-size ${
                                        isActive(nav.path)
                                            ? 'menu-item-icon-active'
                                            : 'menu-item-icon-inactive'
                                    }`}
                                >
                                    {nav.icon}
                                </span>
                                {(isExpanded || isHovered || isMobileOpen) && (
                                    <span className="menu-item-text">{nav.name}</span>
                                )}
                            </Link>
                        )
                    )}
                    {nav.subItems && (isExpanded || isHovered || isMobileOpen) && (
                        <div
                            ref={(el) => {
                                subMenuRefs.current[`${menuType}-${index}`] = el
                            }}
                            className="overflow-hidden transition-all duration-300"
                            style={{
                                height:
                                    openSubmenu?.type === menuType && openSubmenu?.index === index
                                        ? `${subMenuHeight[`${menuType}-${index}`]}px`
                                        : '0px',
                            }}
                        >
                            <ul className="mt-2 space-y-1 ml-9">
                                {nav.subItems.map((subItem) => (
                                    <li key={subItem.name}>
                                        <Link
                                            to={subItem.path}
                                            className={`menu-dropdown-item ${
                                                isActive(subItem.path)
                                                    ? 'menu-dropdown-item-active'
                                                    : 'menu-dropdown-item-inactive'
                                            }`}
                                            state={subItem?.state}
                                        >
                                            {subItem.name}
                                            <span className="flex items-center gap-1 ml-auto">
                                                {subItem.new && (
                                                    <span
                                                        className={`ml-auto ${
                                                            isActive(subItem.path)
                                                                ? 'menu-dropdown-badge-active'
                                                                : 'menu-dropdown-badge-inactive'
                                                        } menu-dropdown-badge`}
                                                    >
                                                        new
                                                    </span>
                                                )}
                                            </span>
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}
                </li>
            ))}
        </ul>
    )

    return (
        <aside
            className={`fixed mt-16 flex flex-col lg:mt-0 top-0 px-3 left-0 bg-blue-200 dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-50 border-r border-gray-200 
            ${isExpanded || isMobileOpen ? 'w-[250px]' : isHovered ? 'w-[290px]' : 'w-[70px]'}
            ${isMobileOpen ? 'translate-x-0' : '-translate-x-full'}
            lg:translate-x-0`}
            onMouseEnter={() => !isExpanded && setIsHovered(true)}
            onMouseLeave={() => setIsHovered(false)}
        >
            <div
                className={`py-4 flex dark:text-gray-200 ${
                    !isExpanded && !isHovered ? 'lg:justify-center' : 'justify-center'
                }`}
            >
                <Link to="/">
                    {isExpanded || isHovered || isMobileOpen ? <>abi-CMS</> : <>CMS</>}
                </Link>
            </div>
            <div className="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
                <nav className="mb-6">
                    <div className="flex flex-col gap-4">
                        <div>
                            {renderMenuItems(menuItems.length ? menuItems : navItems, 'main')}
                        </div>
                    </div>
                </nav>
            </div>
        </aside>
    )
}
