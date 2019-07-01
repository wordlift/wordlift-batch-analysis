(function(settings) {
	const form = document.getElementById("wlba-form");
	const progressBarEl = document.getElementById("wlba-progress-bar");

	const limit = parseInt(settings.limit);

	let index = 0;

	const request = function(
		offset,
		limit,
		nonce,
		links,
		includeAnnotated,
		minOccurrences,
		postType
	) {
		// Record the last offset, for resume operations.
		index = offset;

		return wp.ajax
			.post(settings.action, {
				offset: offset,
				limit: limit,
				links: links,
				include_annotated: includeAnnotated,
				min_occurrences: minOccurrences,
				post_type: postType,
				_ajax_nonce: nonce
			})
			.fail(function(data) {
				alert("An error occurred. Please retry.");
			})
			.done(function(data) {
				const current = (100 * (data.index + 1)) / data.count;
				progressBarEl.style.width = current + "%";
				progressBarEl.innerText = Math.round(current) + "%";

				return data;
			})
			.then(function(data) {
				if (!data.complete)
					return request(
						parseInt(data.index) + limit,
						limit,
						data.nonce,
						links,
						includeAnnotated,
						minOccurrences,
						postType
					);
			});
	};

	form.addEventListener("submit", function(e) {
		e.preventDefault();

		const links = form.querySelector("input[name='links']:checked").value;
		const includeAnnotated = form.querySelector(
			"input[name='include_annotated']:checked"
		).value;
		const minOccurrences = form.querySelector(
			"input[name='min_occurrences']"
		).value;
		const postType = form.querySelector("input[name='post_type']").value;

		form.style.display = "none";
		progressBarEl.parentElement.style.display = "block";

		// At start-up index is 0. Then it tracks the last index, which is useful for resume operations.
		request(
			index,
			limit,
			settings._ajax_nonce,
			links,
			includeAnnotated,
			minOccurrences,
			postType
		);
	});
})(window["wlbaBatchAnalysisSettings"]);
