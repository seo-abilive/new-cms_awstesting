import { forwardRef, useEffect, useState } from 'react'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { Card, CardBody, CardHeader } from '@/utils/components/ui/card'
import { BreadNavigation } from '@/utils/components/ui/breadcrumb'
import { Button, ButtonGroup } from '@/utils/components/ui/button'
import { HiOutlineArrowCircleLeft, HiOutlineRefresh, HiOutlineSave } from 'react-icons/hi'
import { useAxios } from '@/utils/hooks/useAxios'
import { getUrlParams } from '@/utils/common'
import { Alert, Spinner } from 'flowbite-react'
import { SortTable } from '@/utils/components/ui/table'
import { toast } from 'sonner'

/**
 * 並び替え表示用コンポーネント
 */
export const ResourceSort = forwardRef(({ options }, ref) => {
    const {
        breads = [],
        config,
        columns,
        baseParams,
        addScopedColumns = {},
        skeletonRow = 10,
    } = options
    const { navigateTo } = useNavigation()
    const { data, error, loading, sendRequest } = useAxios()
    const [isInitialized, setIsInitialized] = useState(false)
    const { error: sortError, loading: sortLoading, sendRequest: sortSendRequest } = useAxios()

    useEffect(() => {
        setIsInitialized(true)
    }, [])

    useEffect(() => {
        if (!isInitialized) return

        // 全件取得
        fetchAll()
    }, [isInitialized])

    const fetchAll = () => {
        sendRequest({
            method: 'get',
            url: `${config.end_point}/resource?` + getUrlParams(baseParams),
        })
    }

    // APIから取得したデータをitemsとして使用
    const items = data?.payload?.data || []
    let sortedItems = items

    const scopedColumns = {
        ...addScopedColumns,
    }

    const onSort = (updated) => {
        sortedItems = updated
    }

    const onSave = async () => {
        // 並び順取得
        const sortData = sortedItems.map((item, key) => {
            return item.id
        })

        // 並び替え実行
        await sortSendRequest({
            method: 'post',
            url: `${config.end_point}/sort`,
            data: { sort_ids: sortData },
        })

        if (!sortLoading && !sortError) {
            // 成功したらトースター表示
            toast.success('並び替えが完了しました')
        }
    }

    return (
        <>
            <Card>
                <CardHeader>
                    <div className="flex items-center justify-between w-full">
                        <div className="flex items-center gap-4 flex-1">
                            <BreadNavigation breads={breads} />
                        </div>
                        <div>
                            <ButtonGroup>
                                <Button
                                    size="xs"
                                    outline
                                    onClick={() => fetchAll()}
                                    disabled={loading || sortLoading}
                                >
                                    {loading || sortLoading ? (
                                        <Spinner size="sm" />
                                    ) : (
                                        <HiOutlineRefresh className="me-1" />
                                    )}
                                    更新
                                </Button>
                                <Button
                                    size="xs"
                                    outline
                                    onClick={() => onSave()}
                                    disabled={loading || sortLoading}
                                >
                                    {loading || sortLoading ? (
                                        <Spinner size="sm" />
                                    ) : (
                                        <HiOutlineSave className="me-1" />
                                    )}
                                    並び替え保存
                                </Button>
                                <Button size="xs" outline onClick={() => navigateTo(config.path)}>
                                    <HiOutlineArrowCircleLeft className="me-0.5" />
                                    一覧に戻る
                                </Button>
                            </ButtonGroup>
                        </div>
                    </div>
                </CardHeader>
                <CardBody>
                    {error && (
                        <Alert color="failure" className="mb-4">
                            データの読み込み中にエラーが発生しました: {error.message}
                        </Alert>
                    )}
                    {sortError && (
                        <Alert color="failure" className="mb-4">
                            並び替え処理時にエラーが発生しました: {sortError.message}
                        </Alert>
                    )}
                    <SortTable
                        columns={columns}
                        defaultItems={items}
                        scopedColumns={scopedColumns}
                        loading={loading}
                        skeletonRow={skeletonRow}
                        nodata={
                            <div className="p-5 text-center text-gray-500 dark:text-gray-400">
                                データがありません
                            </div>
                        }
                        onChange={onSort}
                    />
                </CardBody>
            </Card>
        </>
    )
})
