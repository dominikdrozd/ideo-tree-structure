<?php

namespace App\Http\Controllers;

use App\Http\Requests\NodeRequest;
use App\Models\Node;
use Illuminate\Http\Request;

class NodeController extends Controller
{

    /**
     * Index action that render tree.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Request $request) {
        $tree = Node::getTree($request->boolean('orderDesc'));
        return view('nodes.index', compact('tree'));
    }

    /**
     * Load part of tree.
     *
     * @param Node $node
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show(Node $node, Request $request) {
        $tree = $node->loadTree($request->boolean('orderDesc'));
        return view('nodes.show', compact('tree'));
    }

    /**
     * Create form
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create() {
        $nodeList = Node::all();
        return view('nodes.create', compact('nodeList'));
    }

    /**
     * Store node data.
     *
     * @param NodeRequest $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function store(NodeRequest $request) {
        $validated = $request->validated();
        Node::create($validated);
        return back()->with('success', 'node created.');
    }

    /**
     * Edit form.
     *
     * @param NodeRequest $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit(Node $node) {
        $descendantsIds = $node->descendants()->pluck('id');
        $descendantsIds->push($node->id);

        $nodeList = Node::whereNotIn('id', $descendantsIds)->get();
        return view('nodes.edit', compact('node', 'nodeList'));
    }

    /**
     * Store node data.
     *
     * @param Node $node
     * @param NodeRequest $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function update(Node $node, NodeRequest $request) {
        $validated = $request->validated();
        $node->updateWithPathWithDescendants($validated);
        return back()->with('success', 'node updated.');
    }

    /**
     * Delete node.
     *
     * @param Node $node
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Node $node) {
        $descendants = $node->descendants();

        if($descendants->count() <= 0) {
            $node->delete();
            return back()->with('success', 'node deleted.');
        }

        $node->deleteWithDescendants();
        return back()->with('success', 'node deleted with descendants.');
    }

}
