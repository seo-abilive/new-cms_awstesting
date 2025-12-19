import { ResourceIndex } from '@/utils/components/common/ResourceIndex'
import { toast } from 'sonner'
import { HiOutlineClipboardCopy } from 'react-icons/hi'
import config from '@/config/configLoader'
import { useModPage } from '../utils/context/ModPageContext'
import { useAuth } from '@/utils/context/AuthContext'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'
import { useAxios } from '@/utils/hooks/useAxios'
import { useEffect, useMemo } from 'react'

export const Index = () => {
    const { config: contactSettingConfig } = useModPage()
    const breads = [{ name: contactSettingConfig.name }]
    const columns = [
        { key: 'title', label: 'タイトル' },
        { key: 'actions', label: '', sortable: false, _props: { style: { width: '10%' } } },
    ]
    const { user } = useAuth()
    const { company_alias, facility_alias } = useCompanyFacility()
    const { data: permissionsData, sendRequest: fetchPermissions } = useAxios()

    // 削除権限、並び替え権限、書き込み権限を一度に取得
    useEffect(() => {
        if (company_alias && facility_alias) {
            fetchPermissions({
                method: 'GET',
                url: `user/permissions/check?resource_type=contact_setting&permissions=delete,write,sort&company_alias=${company_alias}&facility_alias=${facility_alias}`,
            })
        }
    }, [company_alias, facility_alias, fetchPermissions])

    const deletePermission = permissionsData?.payload?.delete?.has_permission ?? false
    const deleteScope = permissionsData?.payload?.delete?.scope ?? null
    const sortPermission = permissionsData?.payload?.sort?.has_permission ?? false
    const writePermission = permissionsData?.payload?.write?.has_permission ?? false

    // 削除可能かどうかを判定する関数
    const canDeleteItem = useMemo(() => {
        return (item) => {
            // 削除権限がない場合は非表示
            if (!deletePermission) {
                return false
            }

            // スコープが'own'の場合、自分の投稿のみ削除可能
            if (deleteScope === 'own') {
                const userId = user?.id ? String(user.id) : null
                const itemCreatedBy = item?.created_by ? String(item.created_by) : null
                return userId && itemCreatedBy && userId === itemCreatedBy
            }

            // スコープが'all'の場合は削除可能
            return true
        }
    }, [deletePermission, deleteScope, user?.id])

    return (
        <>
            <ResourceIndex
                options={{
                    breads,
                    config: contactSettingConfig,
                    columns,
                    isNew: writePermission, // 書き込み権限がある場合のみ追加ボタンを表示
                    isSort: sortPermission, // 並び替え権限がある場合のみ表示
                    CustomDeleteComp: ({ item, row, idx, children }) => {
                        if (!canDeleteItem(item)) {
                            return <></>
                        }
                        return <>{children}</>
                    },
                    addDropdownItems: [
                        {
                            name: 'ウィジェットコード取得',
                            icon: HiOutlineClipboardCopy,
                            onClick: (item) => {
                                const origin = window.location.origin
                                const code = `<iframe src="${origin}${config.basename}widget/contact/${item.token}" style="border: none; width: 100%; height: 600px"></iframe>`
                                navigator.clipboard.writeText(code)
                                toast.success('コードをクリップボードにコピーしました')
                            },
                        },
                    ],
                }}
            />
        </>
    )
}
