import { config, userTypeOptions } from '../utils/config'
import { ResourceIndex } from '@/utils/components/common/ResourceIndex'
import { useAxios } from '@/utils/hooks/useAxios'
import { toast } from 'sonner'
import { useState, useRef } from 'react'
import { Modal, ModalBody } from '@/utils/components/ui/modal'
import { Button } from '@/utils/components/ui/button'
import { HiOutlineExclamationCircle } from 'react-icons/hi'

export const Index = () => {
    const breads = [{ name: config.name }]
    const { sendRequest, loading } = useAxios()
    const [showResetModal, setShowResetModal] = useState(false)
    const [resetTargetUser, setResetTargetUser] = useState(null)
    const resourceIndexRef = useRef(null)
    const columns = [
        { key: 'name', label: '名前' },
        { key: 'email', label: 'メールアドレス' },
        { key: 'user_type', label: 'ユーザータイプ' },
        { key: 'status', label: 'ステータス' },
        { key: 'actions', label: '', sortable: false, _props: { style: { width: '10%' } } },
    ]

    const handleResetTwoFactorClick = (item) => {
        setResetTargetUser(item)
        setShowResetModal(true)
    }

    const handleResetTwoFactor = async () => {
        if (!resetTargetUser) return

        const result = await sendRequest({
            method: 'POST',
            url: `${config.end_point}/${resetTargetUser.id}/two-factor/reset`,
        })

        if (result?.success) {
            toast.success('2段階認証の状態をリセットしました。')
            setShowResetModal(false)
            setResetTargetUser(null)
            // 一覧を再取得
            if (resourceIndexRef.current?.refresh) {
                resourceIndexRef.current.refresh()
            }
        }
    }

    return (
        <>
            <ResourceIndex
                ref={resourceIndexRef}
                options={{
                    breads,
                    config,
                    columns,
                    CustomDeleteComp: ({ item, row, idx, children }) => {
                        if (item.is_master) {
                            return <></>
                        }
                        return <>{children}</>
                    },
                    addScopedColumns: {
                        user_type: (item, row, idx) => {
                            return (
                                <td>
                                    {
                                        userTypeOptions.find(
                                            (option) => option.value === item.user_type
                                        )?.label
                                    }
                                </td>
                            )
                        },
                        status: (item, row, idx) => {
                            return <td>{item.status ? 'ON' : 'OFF'}</td>
                        },
                    },
                    addDropdownItems: [
                        {
                            name: '2段階認証をリセット',
                            onClick: (item) => {
                                handleResetTwoFactorClick(item)
                            },
                            show: (item) => {
                                // is_masterの場合は表示しない
                                return !item.is_master
                            },
                        },
                    ],
                }}
            />
            <Modal show={showResetModal} onClose={() => setShowResetModal(false)} size="md">
                <ModalBody>
                    <div className="text-center">
                        <HiOutlineExclamationCircle className="mx-auto mb-4 h-14 w-14 text-yellow-400 dark:text-gray-200" />
                        <h3 className="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                            2段階認証の状態をリセットしますか？
                        </h3>
                        <p className="mb-5 text-sm text-gray-500 dark:text-gray-400">
                            次回ログイン時に認証コードが要求されます。
                        </p>
                        <div className="flex justify-center gap-4">
                            <Button
                                color="warning"
                                onClick={handleResetTwoFactor}
                                disabled={loading}
                            >
                                {loading ? '処理中...' : 'リセット'}
                            </Button>
                            <Button color="alternative" onClick={() => setShowResetModal(false)}>
                                キャンセル
                            </Button>
                        </div>
                    </div>
                </ModalBody>
            </Modal>
        </>
    )
}
