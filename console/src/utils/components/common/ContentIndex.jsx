import { useEffect, useState, useRef, useMemo } from 'react'
import { ResourceIndex } from './ResourceIndex'
import { useAuth } from '@/utils/context/AuthContext'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'
import { useAxios } from '@/utils/hooks/useAxios'

export const ContentIndex = ({
    breads = [],
    config,
    contentModel,
    options,
    defaultColumns = [],
    defaultAddScopedColumns = {},
    isActions = true,
    onRefreshModelData,
}) => {
    const [columns, setColumns] = useState([])
    const [addScopedColumns, setAddScopedColumns] = useState({})
    const resourceIndexRef = useRef(null)
    const { user } = useAuth()
    const { company_alias, facility_alias } = useCompanyFacility()
    const { data: permissionsData, sendRequest: fetchPermissions } = useAxios()

    // 削除権限、並び替え権限、書き込み権限を一度に取得
    useEffect(() => {
        if (contentModel?.alias && company_alias && facility_alias) {
            fetchPermissions({
                method: 'GET',
                url: `user/permissions/check?resource_type=content&permissions=delete,write,sort&model_name=${contentModel.alias}&company_alias=${company_alias}&facility_alias=${facility_alias}`,
            })
        }
    }, [contentModel?.alias, company_alias, facility_alias, fetchPermissions])

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

    // カラム設定
    useEffect(() => {
        if (!contentModel) return

        const newColumns = [...defaultColumns]
        const newAddScopedColumns = { ...defaultAddScopedColumns }

        if (isActions) {
            newColumns.push({
                key: 'actions',
                label: '',
                sortable: false,
                _props: { style: { width: '10%' } },
            })
        }

        setColumns(newColumns)
        setAddScopedColumns(newAddScopedColumns)
    }, [contentModel, isActions])

    // 上限に達しているかチェック
    const isMaxContentReached =
        contentModel?.max_content_count !== null &&
        contentModel?.max_content_count !== undefined &&
        contentModel?.current_content_count !== undefined &&
        contentModel.current_content_count >= contentModel.max_content_count

    // 上限に達している場合、または書き込み権限がない場合は追加ボタンを非表示
    const finalOptions = {
        ...options,
        isNew:
            isMaxContentReached || !writePermission
                ? false
                : options?.isNew !== undefined
                ? options.isNew
                : true,
        isSort: sortPermission && (options?.isSort !== undefined ? options.isSort : true), // 並び替え権限がある場合のみ表示
        onAfterDelete: onRefreshModelData, // 削除後にContentModelを再取得
        CustomDeleteComp: ({ item, row, idx, children }) => {
            if (!canDeleteItem(item)) {
                return <></>
            }
            return <>{children}</>
        },
    }

    return (
        <>
            <ResourceIndex
                ref={resourceIndexRef}
                options={{ breads, config, columns, addScopedColumns, ...finalOptions }}
            />
        </>
    )
}
