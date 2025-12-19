import { useEffect, useState, useRef } from 'react'
import { useAxios } from '@/utils/hooks/useAxios'
import { Spinner } from '../spinner'
import {
    Checkbox as FCheckbox,
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeadCell,
    TableRow,
} from 'flowbite-react'
import { Switch } from './Switch'

/**
 * 権限設定フォーム
 */
export const PermissionSettings = ({ endpoint, defaultValue = [], onChange = () => {} }) => {
    const { data, loading, sendRequest } = useAxios()
    const [permissions, setPermissions] = useState({})
    const isInitializedRef = useRef(false)
    const defaultValueRef = useRef(defaultValue)

    useEffect(() => {
        if (endpoint) {
            sendRequest({ url: endpoint, method: 'GET' })
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [endpoint])

    // データ取得後にpermissionsを初期化（初回のみ）
    useEffect(() => {
        if (data?.payload?.data && !isInitializedRef.current) {
            const initialPermissions = {}
            data.payload.data.forEach((item) => {
                const itemKey = `permission_${item.resource_type}_${item.id}`
                // defaultValueから初期値を取得
                const defaultPermission = defaultValueRef.current.find(
                    (p) =>
                        p.resource_type === item.resource_type &&
                        (p.resource_id === item.id || (p.resource_id === null && item.id === null))
                )
                initialPermissions[itemKey] = defaultPermission
                    ? {
                          enabled:
                              defaultPermission.read?.enabled ||
                              defaultPermission.write?.enabled ||
                              defaultPermission.delete?.enabled ||
                              defaultPermission.sort?.enabled ||
                              false,
                          read: {
                              enabled: defaultPermission.read?.enabled || false,
                              scope: defaultPermission.read?.scope || 'all',
                          },
                          write: {
                              enabled: defaultPermission.write?.enabled || false,
                              scope: defaultPermission.write?.scope || 'all',
                          },
                          delete: {
                              enabled: defaultPermission.delete?.enabled || false,
                              scope: defaultPermission.delete?.scope || 'all',
                          },
                          sort: {
                              enabled: defaultPermission.sort?.enabled || false,
                          },
                      }
                    : {
                          enabled: false,
                          read: { enabled: false, scope: 'all' },
                          write: { enabled: false, scope: 'all' },
                          delete: { enabled: false, scope: 'all' },
                          sort: { enabled: false },
                      }
            })
            setPermissions(initialPermissions)
            isInitializedRef.current = true
        }
    }, [data])

    // permissionsが変更されたらonChangeを呼び出す（初期化後のみ）
    useEffect(() => {
        if (
            !data?.payload?.data ||
            Object.keys(permissions).length === 0 ||
            !isInitializedRef.current
        )
            return

        const permissionsArray = data.payload.data.map((item) => {
            const itemKey = `permission_${item.resource_type}_${item.id}`
            const itemPermission = permissions[itemKey] || {
                enabled: false,
                read: { enabled: false, scope: 'all' },
                write: { enabled: false, scope: 'all' },
                delete: { enabled: false, scope: 'all' },
                sort: { enabled: false },
            }

            return {
                resource_type: item.resource_type,
                resource_id: item.id,
                read: {
                    enabled: itemPermission.read?.enabled || false,
                    scope: itemPermission.read?.scope || 'all',
                },
                write: {
                    enabled: itemPermission.write?.enabled || false,
                    scope: itemPermission.write?.scope || 'all',
                },
                delete: {
                    enabled: itemPermission.delete?.enabled || false,
                    scope: itemPermission.delete?.scope || 'all',
                },
                sort: {
                    enabled: itemPermission.sort?.enabled || false,
                },
            }
        })

        onChange(permissionsArray)
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [permissions, data])

    // 全ての行のチェックボックスの状態を計算
    const allChecked = data?.payload?.data
        ? data.payload.data.every((item) => {
              const itemKey = `permission_${item.resource_type}_${item.id}`
              const itemPermission = permissions[itemKey]
              return itemPermission?.enabled === true
          })
        : false

    const someChecked = data?.payload?.data
        ? data.payload.data.some((item) => {
              const itemKey = `permission_${item.resource_type}_${item.id}`
              const itemPermission = permissions[itemKey]
              return itemPermission?.enabled === true
          })
        : false

    // 見出しのチェックボックスをクリックした時の処理
    const handleHeaderCheckboxChange = (e) => {
        const isChecked = e.target.checked
        const newPermissions = { ...permissions }

        data.payload.data.forEach((item) => {
            const itemKey = `permission_${item.resource_type}_${item.id}`
            const currentPermission = permissions[itemKey] || {
                enabled: false,
                read: { enabled: false, scope: 'all' },
                write: { enabled: false, scope: 'all' },
                delete: { enabled: false, scope: 'all' },
                sort: { enabled: false },
            }

            newPermissions[itemKey] = {
                enabled: isChecked,
                read: {
                    enabled: isChecked,
                    scope: currentPermission.read.scope,
                },
                write: {
                    enabled: isChecked,
                    scope: currentPermission.write.scope,
                },
                delete: {
                    enabled: isChecked,
                    scope: currentPermission.delete.scope,
                },
                sort: {
                    enabled: isChecked,
                },
            }
        })

        setPermissions(newPermissions)
    }

    return (
        <>
            {loading && <Spinner />}
            {!loading && data?.payload?.data && data?.payload?.data?.length === 0 && (
                <div className="text-center text-gray-500">
                    <p>権限設定がありません</p>
                </div>
            )}
            {!loading && data?.payload?.data && (
                <div style={{ maxHeight: '400px', overflowY: 'auto', isolation: 'isolate' }}>
                    <Table className="text-gray-900 dark:text-gray-100" striped>
                        <TableHead
                            style={{ position: 'sticky', top: 0, background: '#fff', zIndex: 10 }}
                        >
                            <TableRow>
                                <TableHeadCell className="w-20 text-center">
                                    <FCheckbox
                                        checked={allChecked}
                                        onChange={handleHeaderCheckboxChange}
                                    />
                                </TableHeadCell>
                                <TableHeadCell>機能名</TableHeadCell>
                                <TableHeadCell colSpan={2}>閲覧</TableHeadCell>
                                <TableHeadCell colSpan={2}>書き込み</TableHeadCell>
                                <TableHeadCell colSpan={2}>削除</TableHeadCell>
                                <TableHeadCell>並び替え</TableHeadCell>
                            </TableRow>
                        </TableHead>

                        <TableBody>
                            {data.payload.data.map((item) => {
                                const itemKey = `permission_${item.resource_type}_${item.id}`
                                const itemPermission = permissions[itemKey] || {
                                    enabled: false,
                                    read: { enabled: false, scope: 'all' },
                                    write: { enabled: false, scope: 'all' },
                                    delete: { enabled: false, scope: 'all' },
                                    sort: { enabled: false },
                                }

                                return (
                                    <TableRow key={item.id}>
                                        <TableCell className="w-10 p-1 align-middle">
                                            <div className="flex items-center justify-center h-full">
                                                <FCheckbox
                                                    checked={itemPermission.enabled}
                                                    onChange={(e) => {
                                                        const isEnabled = e.target.checked
                                                        setPermissions((prev) => ({
                                                            ...prev,
                                                            [itemKey]: {
                                                                ...itemPermission,
                                                                enabled: isEnabled,
                                                                read: {
                                                                    enabled: isEnabled,
                                                                    scope: itemPermission.read
                                                                        .scope,
                                                                },
                                                                write: {
                                                                    enabled: isEnabled,
                                                                    scope: itemPermission.write
                                                                        .scope,
                                                                },
                                                                delete: {
                                                                    enabled: isEnabled,
                                                                    scope: itemPermission.delete
                                                                        .scope,
                                                                },
                                                                sort: {
                                                                    enabled: isEnabled,
                                                                },
                                                            },
                                                        }))
                                                    }}
                                                />
                                            </div>
                                        </TableCell>
                                        <TableCell>{item.title}</TableCell>
                                        <TableCell className="w-10 p-1 align-middle">
                                            <div className="flex items-center justify-center h-full">
                                                <FCheckbox
                                                    className="p-2"
                                                    checked={itemPermission.read.enabled}
                                                    onChange={(e) => {
                                                        setPermissions((prev) => ({
                                                            ...prev,
                                                            [itemKey]: {
                                                                ...prev[itemKey],
                                                                read: {
                                                                    ...prev[itemKey]?.read,
                                                                    enabled: e.target.checked,
                                                                },
                                                            },
                                                        }))
                                                    }}
                                                />
                                            </div>
                                        </TableCell>
                                        <TableCell
                                            className="p-1"
                                            style={{ position: 'relative', zIndex: 1 }}
                                        >
                                            <div className="scale-85 origin-left">
                                                <Switch
                                                    label="自分の投稿のみ"
                                                    defaultValue={
                                                        itemPermission.read.scope === 'own'
                                                    }
                                                    disabled={!itemPermission.read.enabled}
                                                    onChange={(value) => {
                                                        setPermissions((prev) => ({
                                                            ...prev,
                                                            [itemKey]: {
                                                                ...prev[itemKey],
                                                                read: {
                                                                    ...prev[itemKey]?.read,
                                                                    scope: value ? 'own' : 'all',
                                                                },
                                                            },
                                                        }))
                                                    }}
                                                />
                                            </div>
                                        </TableCell>
                                        <TableCell className="w-10 p-1 align-middle">
                                            <div className="flex items-center justify-center h-full">
                                                <FCheckbox
                                                    className="p-2"
                                                    checked={itemPermission.write.enabled}
                                                    onChange={(e) => {
                                                        setPermissions((prev) => ({
                                                            ...prev,
                                                            [itemKey]: {
                                                                ...prev[itemKey],
                                                                write: {
                                                                    ...prev[itemKey]?.write,
                                                                    enabled: e.target.checked,
                                                                },
                                                            },
                                                        }))
                                                    }}
                                                />
                                            </div>
                                        </TableCell>
                                        <TableCell
                                            className="p-1"
                                            style={{ position: 'relative', zIndex: 1 }}
                                        >
                                            <div className="scale-85 origin-left">
                                                <Switch
                                                    label="自分の投稿のみ"
                                                    defaultValue={
                                                        itemPermission.write.scope === 'own'
                                                    }
                                                    disabled={!itemPermission.write.enabled}
                                                    onChange={(value) => {
                                                        setPermissions((prev) => ({
                                                            ...prev,
                                                            [itemKey]: {
                                                                ...prev[itemKey],
                                                                write: {
                                                                    ...prev[itemKey]?.write,
                                                                    scope: value ? 'own' : 'all',
                                                                },
                                                            },
                                                        }))
                                                    }}
                                                />
                                            </div>
                                        </TableCell>
                                        <TableCell className="w-10 p-1 align-middle">
                                            <div className="flex items-center justify-center h-full">
                                                <FCheckbox
                                                    className="p-2"
                                                    checked={itemPermission.delete.enabled}
                                                    onChange={(e) => {
                                                        setPermissions((prev) => ({
                                                            ...prev,
                                                            [itemKey]: {
                                                                ...prev[itemKey],
                                                                delete: {
                                                                    ...prev[itemKey]?.delete,
                                                                    enabled: e.target.checked,
                                                                },
                                                            },
                                                        }))
                                                    }}
                                                />
                                            </div>
                                        </TableCell>
                                        <TableCell
                                            className="p-1"
                                            style={{ position: 'relative', zIndex: 1 }}
                                        >
                                            <div className="scale-85 origin-left">
                                                <Switch
                                                    label="自分の投稿のみ"
                                                    defaultValue={
                                                        itemPermission.delete.scope === 'own'
                                                    }
                                                    disabled={!itemPermission.delete.enabled}
                                                    onChange={(value) => {
                                                        setPermissions((prev) => ({
                                                            ...prev,
                                                            [itemKey]: {
                                                                ...prev[itemKey],
                                                                delete: {
                                                                    ...prev[itemKey]?.delete,
                                                                    scope: value ? 'own' : 'all',
                                                                },
                                                            },
                                                        }))
                                                    }}
                                                />
                                            </div>
                                        </TableCell>
                                        <TableCell className="p-1 align-middle">
                                            <div className="flex items-center justify-left h-full ps-4">
                                                <FCheckbox
                                                    className="p-2"
                                                    checked={itemPermission.sort.enabled}
                                                    onChange={(e) => {
                                                        setPermissions((prev) => ({
                                                            ...prev,
                                                            [itemKey]: {
                                                                ...prev[itemKey],
                                                                sort: {
                                                                    ...prev[itemKey]?.sort,
                                                                    enabled: e.target.checked,
                                                                },
                                                            },
                                                        }))
                                                    }}
                                                />
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                )
                            })}
                        </TableBody>
                    </Table>
                </div>
            )}
        </>
    )
}
