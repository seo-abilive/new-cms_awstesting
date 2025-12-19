import React, { useEffect, useRef, useState, useMemo, useImperativeHandle, forwardRef } from 'react'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { useLocation, useNavigate, useParams } from 'react-router'
import { useAxios } from '@/utils/hooks/useAxios'
import { Dropdown, DropdownItem } from '@/utils/components/ui/dropdown'
import { Button, ButtonGroup } from '@/utils/components/ui/button'
import { MoreDotIcon } from '@/utils/icons'
import { Card, CardBody, CardHeader } from '@/utils/components/ui/card'
import { BreadNavigation } from '@/utils/components/ui/breadcrumb'
import { Spinner } from '@/utils/components/ui/spinner'
import { Alert } from '@/utils/components/ui/alert'
import { ListTable } from '@/utils/components/ui/table'
import { Paginate } from '@/utils/components/ui/paginate'
import { Modal, ModalBody } from '@/utils/components/ui/modal'
import {
    HiOutlineExclamationCircle,
    HiOutlinePencilAlt,
    HiOutlinePlusCircle,
    HiOutlineRefresh,
    HiOutlineXCircle,
    HiOutlineSearch,
    HiOutlineAdjustments,
    HiOutlineArrowUp,
    HiOutlineArrowDown,
    HiOutlineSwitchVertical,
} from 'react-icons/hi'
import { getUrlParams } from '../../common'
import { toast } from 'sonner'

import { useSessionStorage } from '../../hooks/useSessionStorage'
import { Select } from '../ui/form/Select'

/**
 * 汎用的なリソース一覧表示用コンポーネント。
 *
 * @component
 * @param {Object} props
 * @param {Object} props.options コンポーネントの設定オプション
 * @param {Array} [props.options.breads=[]] パンくずリスト表示用配列
 * @param {Object} props.options.config リソースの設定情報
 * @param {string} props.options.config.name リソース名
 * @param {string} props.options.config.end_point APIのエンドポイントURL
 * @param {string} props.options.config.path 新規作成・編集画面のパスベース
 * @param {Array} props.options.columns テーブル列定義の配列
 * @param {boolean} [props.options.isNew=true] 新規作成ボタンを表示するか
 * @param {boolean} [props.options.isEdit=true] 編集アクションを表示するか
 * @param {boolean} [props.options.isDelete=true] 削除アクションを表示するか
 * @param {boolean} [props.options.isSort=true] 並び替えアクションを表示するか
 * @param {Object} [props.options.addScopedColumns={}] テーブルに追加する独自カラム定義
 * @param {Array} [props.options.addDropdownItems=[]] 各行のアクションに追加するドロップダウン項目
 * @param {Function|null} [props.options.customNewAction=null] 新規作成ボタンのカスタム処理関数
 * @param {Function|null} [props.options.customEditAction=null] 編集ボタンのカスタム処理関数
 * @param {Function|null} [props.options.customDeleteAction=null] 削除ボタンのカスタム処理関数
 * @param {Function|null} [props.options.customEditComp=null] 編集ボタンのカスタム処理関数
 * @param {Function|null} [props.options.customDeleteComp=null] 削除ボタンのカスタム処理関数
 * @param {Array} [props.options.addPageActionButtons=[]] ページの追加ボタン項目
 * @param {Object} [props.options.baseParams={}] 一覧取得APIに付与する追加クエリパラメータ
 * @param {Object} [props.options.searchConfig={}] 検索機能の設定
 * @param {boolean} [props.options.searchConfig.enabled=true] 検索機能を有効にするか
 * @param {string} [props.options.searchConfig.placeholder='検索...'] 検索フィールドのプレースホルダー
 * @param {Array} [props.options.searchConfig.searchFields=[]] 検索対象フィールド（空の場合は全フィールド検索）
 * @param {Object} [props.options.AdvancedSearchPanel=null] 詳細検索パネルのコンポーネント
 * @param {Int} [props.options.skeletonRow=10] スケルトンスクリーンの行数
 */
export const ResourceIndex = forwardRef(({ options }, ref) => {
    const {
        breads = [],
        config,
        columns,
        isNew = true,
        isEdit = true,
        isDelete = true,
        isSort = false,
        addScopedColumns = {},
        addDropdownItems = [],
        customNewAction = null,
        customEditAction = null,
        customDeleteAction = null,
        CustomEditComp = ({ item, row, idx, children }) => {
            return <>{children}</>
        },
        CustomDeleteComp = ({ item, row, idx, children }) => {
            return <>{children}</>
        },
        baseParams = {},
        addPageActionButtons = [],
        onAfterDelete = null,
        // 検索機能のオプションを追加
        searchConfig = {
            enabled: true,
            placeholder: '検索...',
            searchFields: [], // 検索対象フィールド（空の場合は全フィールド検索）
        },
        AdvancedSearchPanel = null,
        skeletonRow = 10,
    } = options

    const { navigateTo } = useNavigation()
    const [showModal, setShowModal] = useState(false)
    const [deleteId, setDeleteId] = useState(null)
    const location = useLocation()
    const navigate = useNavigate()
    const params = useParams()

    // 企業エイリアスと施設エイリアスを取得（企業・施設ごとに独立したセッションストレージを保持するため）
    const companyAlias = params.company_alias || ''
    const facilityAlias = params.facility_alias || 'master'
    const storageKeyPrefix = `${config.name}_${companyAlias}_${facilityAlias}`

    const [currentPage, setCurrentPage] = useSessionStorage(`${storageKeyPrefix}_current_page`, 1)
    const [itemsPerPage, setItemsPerPage] = useSessionStorage(
        `${storageKeyPrefix}_items_per_page`,
        10
    )

    const [searchQuery, setSearchQuery] = useSessionStorage(`${storageKeyPrefix}_search_query`, '')
    const [debouncedQuery, setDebouncedQuery] = useState(searchQuery)

    // ソート機能の状態を追加
    const [sortConfig, setSortConfig] = useSessionStorage(`${storageKeyPrefix}_sort_config`, {
        column: null,
        direction: 'asc',
    })

    const [isInitialized, setIsInitialized] = useState(false)
    const prevSearchQueryRef = useRef(null)

    // 詳細検索（フォーム値／適用値）は必ずここで宣言し、以降で参照
    const [advancedValues, setAdvancedValues] = useSessionStorage(
        `${storageKeyPrefix}_advanced_criteria_values`,
        {}
    )
    const [appliedAdvancedValues, setAppliedAdvancedValues] = useSessionStorage(
        `${storageKeyPrefix}_advanced_criteria_applied`,
        {}
    )

    // これ以降で computedSearchParams / useEffect から参照
    const computedSearchParams = useMemo(() => {
        const params = {}
        const q = debouncedQuery?.trim()
        if (q) params['criteria[freeword]'] = q
        if (appliedAdvancedValues && typeof appliedAdvancedValues === 'object') {
            Object.entries(appliedAdvancedValues).forEach(([key, val]) => {
                if (val !== '' && val !== null && typeof val !== 'undefined') {
                    params[`criteria[${key}]`] = val
                }
            })
        }
        // ソートパラメータを追加
        if (sortConfig.column) {
            params['sort'] = sortConfig.column
            params['direction'] = sortConfig.direction
        }
        return params
    }, [debouncedQuery, appliedAdvancedValues, sortConfig])

    const limitOptions = [5, 10, 50, 100]

    // useAxiosフックを呼び出し、一覧取得用のsendRequestを取得
    const { data, error, loading, sendRequest: fetchContentModels } = useAxios()
    const { sendRequest: deleteContentModel } = useAxios()

    // 最後に表示したトーストのメッセージ内容を記憶するref
    const currentMessageRef = useRef(null)

    useEffect(() => {
        if (location.state?.message && location.state.message !== currentMessageRef.current) {
            toast.success(location.state.message)
            currentMessageRef.current = location.state.message
            navigate(location.pathname, { replace: true, state: {} })
        } else if (!location.state?.toast?.message && currentMessageRef.current) {
            currentMessageRef.current = null
        }
    }, [location, navigate])

    // 企業エイリアスまたは施設エイリアスが変わった時に初期化状態をリセット
    useEffect(() => {
        setIsInitialized(false)
    }, [companyAlias, facilityAlias])

    // 初期化（検索語だけ復元。パラメータは算出に変更）
    useEffect(() => {
        if (!isInitialized) {
            prevSearchQueryRef.current = searchQuery
            setDebouncedQuery(searchQuery)
            setIsInitialized(true)
        }
    }, [isInitialized, searchQuery])

    // 初期同期（保存済みの適用値をフォームへ、未保存なら初期値を適用側へ）
    useEffect(() => {
        if (!isInitialized) {
            // 詳細検索フォームの初期値を、まずは「適用中の値」で上書き
            if (appliedAdvancedValues && Object.keys(appliedAdvancedValues).length > 0) {
                // advancedValuesが空なら、適用済みからフォームへ反映
                if (!advancedValues || Object.keys(advancedValues).length === 0) {
                    setAdvancedValues(appliedAdvancedValues)
                }
            } else if (advancedValues && Object.keys(advancedValues).length > 0) {
                // フォーム側にだけ値がある場合は、それを適用側にも反映（リロード後の初回に有効化）
                setAppliedAdvancedValues(advancedValues)
            }
        }
    }, [isInitialized, advancedValues, appliedAdvancedValues])

    // 一覧取得（デバウンス済みの検索語から算出したパラメータを使用）
    useEffect(() => {
        if (!isInitialized) return
        fetchContentModels({
            method: 'get',
            url:
                `${config.end_point}?` +
                getUrlParams({
                    current: currentPage,
                    limit: itemsPerPage,
                    ...baseParams,
                    ...computedSearchParams,
                }),
        })
    }, [currentPage, itemsPerPage, computedSearchParams, isInitialized])

    // 検索語のデバウンスとページング制御（語が変わった時だけ1ページ目へ）
    useEffect(() => {
        if (!isInitialized) return
        const t = setTimeout(() => {
            if (prevSearchQueryRef.current !== searchQuery) {
                setCurrentPage(1)
                prevSearchQueryRef.current = searchQuery
            }
            if (debouncedQuery !== searchQuery) {
                setDebouncedQuery(searchQuery)
            }
        }, 500)
        return () => clearTimeout(t)
    }, [searchQuery, isInitialized, debouncedQuery])

    const handleDelete = async () => {
        try {
            await deleteContentModel({
                method: 'DELETE',
                url: `${config.end_point}/${deleteId}`,
            })
            setShowModal(false)
            toast.success('削除しました')

            if (items.length === 1 && currentPage > 1) {
                setCurrentPage(currentPage - 1)
            } else {
                fetchContentModels({
                    method: 'get',
                    url:
                        `${config.end_point}?` +
                        getUrlParams({
                            current: currentPage,
                            limit: itemsPerPage,
                            ...baseParams,
                            ...computedSearchParams,
                        }),
                })
            }

            // 削除後のコールバックを実行
            if (onAfterDelete) {
                onAfterDelete()
            }
        } catch (err) {
            toast.error('削除に失敗しました')
        }
    }

    // APIから取得したデータをitemsとして使用
    const items = data?.payload?.data || []
    const totalPages = data?.payload?.pages || 1
    const totalItems = data?.payload?.total || 0

    // 表示件数テキストの計算
    const startItem = (currentPage - 1) * itemsPerPage + 1
    const endItem = Math.min(currentPage * itemsPerPage, totalItems)

    const scopedColumns = {
        ...addScopedColumns,
        actions: (item, row, idx) => {
            return (
                <td key={idx}>
                    <div className="flex justify-end">
                        <Dropdown
                            label=""
                            dismissOnClick={false}
                            renderTrigger={() => (
                                <Button
                                    color={'light'}
                                    outline
                                    size="sm"
                                    className="focus:ring-0 focus:outline-none active:ring-0"
                                >
                                    <MoreDotIcon />
                                </Button>
                            )}
                        >
                            {isEdit && (
                                <CustomEditComp item={item} row={row} idx={idx}>
                                    <DropdownItem
                                        onClick={() => {
                                            if (customEditAction) {
                                                customEditAction(item, row, idx)
                                            } else {
                                                navigateTo(`${config.path}/edit/${item.id}`)
                                            }
                                        }}
                                        icon={HiOutlinePencilAlt}
                                    >
                                        編集
                                    </DropdownItem>
                                </CustomEditComp>
                            )}
                            {isDelete && (
                                <CustomDeleteComp item={item} row={row} idx={idx}>
                                    <DropdownItem
                                        onClick={() => {
                                            if (customDeleteAction) {
                                                customDeleteAction(item, row, idx)
                                            } else {
                                                setDeleteId(item.id)
                                                setShowModal(true)
                                            }
                                        }}
                                        icon={HiOutlineXCircle}
                                        className="text-red-800"
                                    >
                                        削除
                                    </DropdownItem>
                                </CustomDeleteComp>
                            )}
                            {React.Children.toArray(
                                addDropdownItems
                                    .filter((dropdown) => {
                                        // showプロパティがある場合は条件をチェック
                                        if (typeof dropdown.show === 'function') {
                                            return dropdown.show(item, row)
                                        }
                                        return true
                                    })
                                    .map((dropdown, index) => {
                                        const { name, onClick = () => {}, show, ...rest } = dropdown
                                        return (
                                            <DropdownItem
                                                key={index}
                                                onClick={() => onClick(item, row)}
                                                {...rest}
                                            >
                                                {name}
                                            </DropdownItem>
                                        )
                                    })
                            )}
                        </Dropdown>
                    </div>
                </td>
            )
        },
    }

    // 検索フィールドのクリア処理を修正
    const handleClearSearch = () => {
        setSearchQuery('')
        setCurrentPage(1)
    }

    const [showAdvanced, setShowAdvanced] = useState(false)

    // ソート処理を追加
    const handleSort = (column, direction) => {
        if (column === null && direction === null) {
            // クリアの場合
            setSortConfig({ column: null, direction: null })
        } else {
            setSortConfig({ column, direction })
        }
        setCurrentPage(1) // ソート時は1ページ目に戻る
    }

    const getSortIcon = (column) => {
        if (column.sortable === false) return null

        if (sortConfig.column === column.key) {
            if (sortConfig.direction === 'asc') {
                return <HiOutlineArrowUp className="w-4 h-4 ml-1" />
            } else if (sortConfig.direction === 'desc') {
                return <HiOutlineArrowDown className="w-4 h-4 ml-1" />
            }
        }
        return <HiOutlineSwitchVertical className="w-4 h-4 ml-1 text-gray-400" />
    }

    // 外部から呼び出せるメソッドを定義
    useImperativeHandle(
        ref,
        () => ({
            refresh: () => {
                fetchContentModels({
                    method: 'get',
                    url:
                        `${config.end_point}?` +
                        getUrlParams({
                            current: currentPage,
                            limit: itemsPerPage,
                            ...baseParams,
                            ...computedSearchParams,
                        }),
                })
            },
        }),
        [config.end_point, currentPage, itemsPerPage, baseParams, computedSearchParams]
    )

    return (
        <>
            <Card>
                <CardHeader>
                    <div className="flex items-center justify-between w-full">
                        <div className="flex items-center gap-4 flex-1">
                            <BreadNavigation breads={breads} />
                            {/* 検索フィールドを追加 */}
                            {searchConfig.enabled && (
                                <div className="flex items-stretch flex-1 max-w-md">
                                    <div className="relative flex-1">
                                        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <HiOutlineSearch className="h-4 w-4 text-gray-400" />
                                        </div>
                                        <input
                                            type="text"
                                            className={`block w-full pl-10 pr-9 py-2 border border-gray-300 rounded-l-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-25 ${
                                                !AdvancedSearchPanel ? 'rounded-r-md' : ''
                                            }`}
                                            placeholder={searchConfig.placeholder}
                                            value={searchQuery}
                                            onChange={(e) => setSearchQuery(e.target.value)}
                                        />
                                        {searchQuery && (
                                            <button
                                                type="button"
                                                className="absolute inset-y-0 right-0 pr-2 flex items-center"
                                                onClick={handleClearSearch}
                                                aria-label="クリア"
                                            >
                                                <HiOutlineXCircle className="h-4 w-4 text-gray-400 hover:text-gray-600" />
                                            </button>
                                        )}
                                    </div>
                                    {AdvancedSearchPanel && (
                                        <button
                                            type="button"
                                            onClick={() => setShowAdvanced(true)}
                                            className="inline-flex items-center px-3 border border-l-0 border-gray-300 rounded-r-md text-sm bg-white text-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-650 "
                                            aria-label="詳細検索を開く"
                                            title="詳細検索"
                                        >
                                            <HiOutlineAdjustments className="h-5 w-5" />
                                        </button>
                                    )}
                                </div>
                            )}
                        </div>
                        <div>
                            <ButtonGroup>
                                <Button
                                    size="xs"
                                    outline
                                    onClick={() =>
                                        fetchContentModels({
                                            method: 'get',
                                            url:
                                                `${config.end_point}?` +
                                                getUrlParams({
                                                    current: currentPage,
                                                    limit: itemsPerPage,
                                                    ...baseParams,
                                                    ...computedSearchParams,
                                                }),
                                        })
                                    }
                                    disabled={loading}
                                >
                                    {loading ? (
                                        <Spinner size="sm" />
                                    ) : (
                                        <HiOutlineRefresh className="me-1" />
                                    )}
                                    更新
                                </Button>
                                {isNew && (
                                    <Button
                                        size="xs"
                                        outline
                                        onClick={() => {
                                            if (!customNewAction) {
                                                navigateTo(config.path + '/new')
                                            } else {
                                                customNewAction()
                                            }
                                        }}
                                    >
                                        <HiOutlinePlusCircle className="me-1" />
                                        追加
                                    </Button>
                                )}
                                {isSort && (
                                    <Button
                                        size="xs"
                                        outline
                                        onClick={() => {
                                            navigateTo(config.path + '/sort')
                                        }}
                                    >
                                        <HiOutlineSwitchVertical className="me-1" />
                                        並び替え
                                    </Button>
                                )}
                                {React.Children.toArray(
                                    addPageActionButtons.map((AddButton) => {
                                        return (
                                            <>
                                                <AddButton />
                                            </>
                                        )
                                    })
                                )}
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
                    <ListTable
                        columns={columns}
                        items={items}
                        scopedColumns={scopedColumns}
                        sortConfig={sortConfig}
                        onSort={handleSort}
                        loading={loading}
                        nodata={
                            <div className="p-5 text-center text-gray-500 dark:text-gray-400">
                                データがありません
                            </div>
                        }
                        skeletonRow={skeletonRow}
                    />
                    <div className="flex items-center justify-between w-full mt-2 pt-2 border-t">
                        <div className="flex items-center">
                            <p className="text-sm font-light text-gray-500 me-2">
                                {totalItems > 0
                                    ? `${totalItems}件中${startItem}〜${endItem}件表示`
                                    : '0件中0〜0件表示'}
                            </p>
                        </div>
                        <div className="flex items-center">
                            <div className="w-28 me-2">
                                <Select
                                    value={itemsPerPage}
                                    onChange={(value) => {
                                        setItemsPerPage(parseInt(value, 10))
                                        setCurrentPage(1)
                                    }}
                                    items={limitOptions.map((limit) => ({
                                        value: limit,
                                        label: `${limit}件`,
                                    }))}
                                    style={{ fontSize: '0.8rem', padding: '5px' }}
                                />
                            </div>
                            <Paginate
                                currentPage={currentPage}
                                totalPages={totalPages}
                                onPageChange={setCurrentPage}
                            />
                        </div>
                    </div>
                </CardBody>
            </Card>
            {/* 詳細検索パネル */}
            {showAdvanced && (
                <>
                    <div
                        className="fixed inset-0 z-40 bg-black/30"
                        onClick={() => setShowAdvanced(false)}
                    />
                    <div className="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white dark:bg-gray-800 shadow-xl transform transition-transform duration-300 translate-x-0">
                        <div className="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 className="text-base font-semibold text-gray-500 dark:text-gray-200">
                                詳細検索
                            </h3>
                            <button
                                type="button"
                                className="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white"
                                onClick={() => setShowAdvanced(false)}
                            >
                                <HiOutlineXCircle className="h-5 w-5" />
                            </button>
                        </div>
                        <div className="p-4 overflow-y-auto">
                            {AdvancedSearchPanel ? (
                                <AdvancedSearchPanel
                                    values={advancedValues}
                                    onChange={(name, value) => {
                                        setAdvancedValues((prev) => ({ ...prev, [name]: value }))
                                    }}
                                    onApply={() => {
                                        setAppliedAdvancedValues(advancedValues)
                                        setCurrentPage(1)
                                        setShowAdvanced(false)
                                    }}
                                    onClear={() => {
                                        setAdvancedValues({})
                                        setAppliedAdvancedValues({})
                                        setCurrentPage(1)
                                    }}
                                    close={() => setShowAdvanced(false)}
                                />
                            ) : (
                                <div className="text-sm text-gray-500 dark:text-gray-300">
                                    このパネルはページごとにカスタマイズできます（`options.AdvancedSearchPanel`
                                    を渡してください）。
                                </div>
                            )}
                        </div>
                    </div>
                </>
            )}
            <Modal show={showModal} onClose={() => setShowModal(false)} size="md">
                <ModalBody>
                    <div className="text-center">
                        <HiOutlineExclamationCircle className="mx-auto mb-4 h-14 w-14 text-red-400 dark:text-gray-200" />
                        <h3 className="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                            削除しますか？
                        </h3>
                        <div className="flex justify-center gap-4">
                            <Button color="red" onClick={handleDelete}>
                                はい
                            </Button>
                            <Button color="alternative" onClick={() => setShowModal(false)}>
                                キャンセル
                            </Button>
                        </div>
                    </div>
                </ModalBody>
            </Modal>
        </>
    )
})
