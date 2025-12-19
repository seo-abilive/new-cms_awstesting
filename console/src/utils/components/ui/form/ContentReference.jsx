import { useEffect, useState } from 'react'
import { Button } from '../button'
import { Modal, ModalBody, ModalHeader } from '../modal'
import { ContentIndex } from '../../common/ContentIndex'
import { useAxios } from '@/utils/hooks/useAxios'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'

/**
 * ContentReferenceの状態管理ロジックをカスタムフックに抽出
 */
const useContentReference = (defaultValue, contentReferenceModel) => {
    const [selected, setSelected] = useState(
        defaultValue && typeof defaultValue === 'object' ? defaultValue : null
    )
    const [initializing, setInitializing] = useState(!!defaultValue)
    const { data: showData, sendRequest: fetchContentShow } = useAxios()
    const { facility_alias, company_alias } = useCompanyFacility()

    useEffect(() => {
        if (!contentReferenceModel?.alias) return

        // オブジェクトが来た場合はそのまま反映
        if (defaultValue && typeof defaultValue === 'object') {
            if (selected?.id !== defaultValue.id) {
                setSelected(defaultValue)
            }
            setInitializing(false)
            return
        }

        // 未設定
        if (!defaultValue) {
            setSelected(null)
            setInitializing(false)
            return
        }

        // IDが来た場合は詳細取得
        if (typeof defaultValue !== 'object') {
            if (!selected || selected.id !== defaultValue) {
                setInitializing(true)
                fetchContentShow({
                    method: 'get',
                    url: `${company_alias}/${facility_alias}/content/${contentReferenceModel.alias}/${defaultValue}`,
                })
            } else {
                setInitializing(false)
            }
        }
    }, [defaultValue, contentReferenceModel, fetchContentShow])

    useEffect(() => {
        const item = showData?.payload?.data
        if (item) {
            setSelected(item)
        }
        if (showData) {
            setInitializing(false)
        }
    }, [showData])

    return { initializing, selected, setSelected }
}

/**
 * ContentReference component opens a modal to select content from a list.
 */
export const ContentReference = ({
    contentReferenceModel,
    defaultValue = null,
    onChange,
    readonly = false,
    disabled = false,
    ...props
}) => {
    const [showModal, setShowModal] = useState(false)
    const { initializing, selected, setSelected } = useContentReference(
        defaultValue,
        contentReferenceModel
    )
    const { company_alias, facility_alias } = useCompanyFacility()

    // 先頭の is_list_heading フィールドを取得
    const headingField = contentReferenceModel?.fields?.find((f) => f?.is_list_heading)

    const defaultColumns = [
        { key: 'btns', label: '', sortable: false, _props: { style: { width: '8%' } } },
    ]
    const addScopedColumns = {
        btns: (item, row, idx) => {
            return (
                <td key={idx}>
                    {!readonly && !disabled && (
                        <Button
                            onClick={() => {
                                handleSelect(item)
                            }}
                            outline
                            size="xs"
                        >
                            選択
                        </Button>
                    )}
                </td>
            )
        },
    }

    // 選択
    const handleSelect = (item) => {
        setSelected(item)
        onChange ? onChange(item.id) : null
        setShowModal(false)
    }

    // クリア
    const handleClear = () => {
        setSelected(null)
        onChange ? onChange(null) : null
    }

    // 選択表示用（先頭 is_list_heading の値を表示）
    const renderSelectedHeading = () => {
        if (!selected || !headingField) return null
        const key = headingField.field_id
        const value = selected[key]
        if (headingField.field_type === 'media_image') {
            const url = value?.file_url
            return (
                <div className="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                    {url ? (
                        <img
                            src={url}
                            alt="thumb"
                            className="w-12 h-12 object-cover rounded border"
                        />
                    ) : (
                        <div className="w-12 h-12 rounded border bg-gray-100" />
                    )}
                    <div className="font-medium">{selected?.id}</div>
                </div>
            )
        }
        return (
            <div className="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-200">
                <div className="font-medium truncate max-w-[320px]">{value ?? ''}</div>
                <span className="text-xs text-gray-500">(ID: {selected?.id})</span>
            </div>
        )
    }

    return (
        <>
            <div className="flex items-center gap-3">
                {initializing ? (
                    <div className="h-6 bg-gray-200 dark:bg-gray-700 rounded w-40 animate-pulse" />
                ) : !selected ? (
                    !readonly &&
                    !disabled && (
                        <Button onClick={() => setShowModal(true)} outline size="xs">
                            記事を選択する
                        </Button>
                    )
                ) : (
                    <>
                        {renderSelectedHeading()}
                        {!readonly && !disabled && (
                            <>
                                <Button size="xs" color="light" onClick={handleClear}>
                                    クリア
                                </Button>
                                <Button size="xs" outline onClick={() => setShowModal(true)}>
                                    変更
                                </Button>
                            </>
                        )}
                    </>
                )}
            </div>
            <Modal show={showModal} onClose={() => setShowModal(false)} size="5xl">
                <ModalHeader>記事を選択する</ModalHeader>
                <ModalBody>
                    <ContentIndex
                        breads={[]}
                        config={{
                            end_point: `${company_alias}/${facility_alias}/content/${contentReferenceModel.alias}`,
                        }}
                        contentModel={contentReferenceModel}
                        defaultColumns={defaultColumns}
                        defaultAddScopedColumns={addScopedColumns}
                        isActions={false}
                        options={{
                            isNew: false,
                        }}
                    />
                </ModalBody>
            </Modal>
        </>
    )
}
