import { Link } from 'react-router'
import { UserIcon } from '@/utils/icons'
import {
    Dropdown,
    DropdownDivider,
    DropdownHeader,
    DropdownItem,
} from '@/utils/components/ui/dropdown'
import { HiCog, HiLogout } from 'react-icons/hi'
import { useAxios } from '@/utils/hooks/useAxios'
import { config } from '@/core/user/utils/config'
import { Spinner } from '../ui/spinner'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { useEffect, useState } from 'react'
import { useAuth } from '@/utils/context/AuthContext'

export const UserDropdown = () => {
    const { sendRequest, loading } = useAxios()
    const { navigateTo } = useNavigation()
    const { user, refresh, setUser } = useAuth()

    useEffect(() => {
        // 初期表示は AuthProvider が取得するので、ここでは不要
    }, [])

    const handleLogout = async () => {
        const result = await sendRequest({
            method: 'POST',
            url: `${config.end_point}/logout`,
        })

        if (result?.success) {
            setUser(null)
            navigateTo('/login')
        }
    }

    return (
        <Dropdown
            renderTrigger={() => (
                <button className="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-8 w-8 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">
                    <UserIcon />
                </button>
            )}
            dismissOnClick={false}
        >
            <DropdownHeader className="text-center">
                <span className="block font-medium text-gray-700 text-theme-sm dark:text-gray-400">
                    {user?.name || 'ログイン中'}
                </span>
                <span className="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">
                    {user?.email || ''}
                </span>
            </DropdownHeader>
            <DropdownItem icon={HiCog} onClick={() => navigateTo('/account')}>
                編集
            </DropdownItem>
            <DropdownDivider />
            <DropdownItem icon={HiLogout} onClick={handleLogout} disabled={loading}>
                {loading ? <Spinner className="size-4" /> : 'ログアウト'}
            </DropdownItem>
        </Dropdown>
    )
}
