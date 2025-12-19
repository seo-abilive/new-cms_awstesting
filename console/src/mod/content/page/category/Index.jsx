import { useContent } from '@/mod/content/utils/context/ContentContext'
import { ResourceIndex } from '@/utils/components/common/ResourceIndex'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { HiOutlineArrowCircleLeft, HiOutlineCloud } from 'react-icons/hi'
import { Button } from '@/utils/components/ui/button'
import { useRef } from 'react'
import { CategoriesApiPreviewModal } from '../../utils/components/CategoriesApiPreviewModal'

export const Index = () => {
    const { getCateConfig, modelData } = useContent()
    const { navigateTo } = useNavigation()
    const config = getCateConfig()
    const breads = [{ name: config.name, path: config.parent_path }, { name: 'カテゴリ' }]

    const categoriesApiPreviewModalRef = useRef(null)

    const columns = [
        { key: 'title', label: 'タイトル' },
        { key: 'actions', label: '', sortable: false, _props: { style: { width: '10%' } } },
    ]

    return (
        <>
            {modelData && (
                <>
                    <ResourceIndex
                        options={{
                            breads,
                            config,
                            columns,
                            isSort: true,
                            addPageActionButtons: [
                                () => {
                                    return (
                                        <Button
                                            size="xs"
                                            outline
                                            onClick={() => navigateTo(config.parent_path)}
                                        >
                                            <HiOutlineArrowCircleLeft className="me-0.5" />
                                            一覧に戻る
                                        </Button>
                                    )
                                },
                                () => {
                                    return (
                                        <Button
                                            size="xs"
                                            outline
                                            onClick={() =>
                                                categoriesApiPreviewModalRef.current?.show()
                                            }
                                        >
                                            <HiOutlineCloud className="me-0.5" /> APIプレビュー
                                        </Button>
                                    )
                                },
                            ],
                        }}
                    />
                    <CategoriesApiPreviewModal
                        ref={categoriesApiPreviewModalRef}
                        modelData={modelData}
                    />
                </>
            )}
        </>
    )
}
