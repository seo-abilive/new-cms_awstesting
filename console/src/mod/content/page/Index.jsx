import { useEffect, useRef, useState } from 'react'
import { useLocation } from 'react-router'
import { ResourceIndex } from '@/utils/components/common/ResourceIndex'
import { useContent } from '@/mod/content/utils/context/ContentContext'
import { Button } from '@/utils/components/ui/button'
import { HiOutlineArrowCircleRight, HiOutlineCloud } from 'react-icons/hi'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { ListApiPreviewModal } from '../utils/components/ListApiPreviewModal'
import { DetailApiPreviewModal } from '../utils/components/DetailApiPreviewModal'
import { ContentIndex } from '@/utils/components/common/ContentIndex'

export const Index = () => {
    const { config, modelData, refreshModelData, listConfig } = useContent()
    const breads = [{ name: config.name }]
    const { navigateTo } = useNavigation()
    const location = useLocation()

    const listApiPreviewModalRef = useRef(null)
    const detailApiPreviewModalRef = useRef(null)

    // 記事登録後に戻ってきた時にContentModelを再取得
    useEffect(() => {
        if (location.state?.message && refreshModelData) {
            refreshModelData()
        }
    }, [location.state?.message, refreshModelData])

    return (
        <>
            {modelData && (
                <>
                    <ContentIndex
                        breads={breads}
                        config={config}
                        contentModel={modelData}
                        defaultColumns={listConfig?.columns || []}
                        defaultAddScopedColumns={listConfig?.addScopedColumns || {}}
                        onRefreshModelData={refreshModelData}
                        options={{
                            addPageActionButtons: (() => {
                                let addButtons = []
                                if (modelData.is_use_category) {
                                    addButtons.push(() => {
                                        return (
                                            <Button
                                                size="xs"
                                                outline
                                                onClick={() =>
                                                    navigateTo(config.path + '/category')
                                                }
                                            >
                                                <HiOutlineArrowCircleRight className="me-0.5" />
                                                カテゴリ
                                            </Button>
                                        )
                                    })
                                }

                                addButtons.push(() => {
                                    return (
                                        <Button
                                            size="xs"
                                            outline
                                            onClick={() => listApiPreviewModalRef.current?.show()}
                                        >
                                            <HiOutlineCloud className="me-0.5" /> APIプレビュー
                                        </Button>
                                    )
                                })

                                return addButtons
                            })(),
                            addDropdownItems: [
                                {
                                    name: 'APIプレビュー',
                                    onClick: (item) => {
                                        detailApiPreviewModalRef.current?.show(item.seq_id)
                                    },
                                },
                            ],
                        }}
                    />
                    <ListApiPreviewModal ref={listApiPreviewModalRef} modelData={modelData} />
                    <DetailApiPreviewModal ref={detailApiPreviewModalRef} modelData={modelData} />
                </>
            )}
        </>
    )
}
